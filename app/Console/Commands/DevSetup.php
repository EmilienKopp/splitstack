<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\ManagesTenantDatabases;
use App\Enums\RoleEnum;
use App\Models\Currency;
use App\Models\Landlord\Tenant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Spatie\Permission\PermissionRegistrar;
use WorkOS\Organizations;
use WorkOS\WorkOS;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class DevSetup extends Command
{
    use ManagesTenantDatabases;

    protected $signature = 'dev:setup
                            {--fresh : Drop and recreate everything from scratch}
                            {--skip-workos : Skip WorkOS integration even if configured}
                            {--admin-email=test@example.com : Admin user email}
                            {--admin-password=password : Admin user password}
                            {--admin-name=Dev User : Admin user name}';

    protected $description = 'Set up the complete development environment (idempotent)';

    private string $templateDatabase = 'tenant_template';

    public function handle(): int
    {
        $this->info('Starting dev environment setup...');
        $this->newLine();

        try {
            $this->checkPostgresConnection();
            $this->checkRedisConnection();
        } catch (\Exception $e) {
            return Command::FAILURE;
        }

        $landlordDb = config('database.connections.landlord.database');
        $domain = config('splitstack.dev_tenant_domain', 'qadranio.com');
        $host = $this->deriveHostFromDomain($domain);
        $database = $this->deriveDatabaseFromHost($host);

        if ($this->option('fresh')) {
            if (! $this->handleFresh($database, $landlordDb)) {
                return Command::FAILURE;
            }
        }

        $this->setupLandlordDatabase($landlordDb);
        $this->runLandlordMigrations();
        $this->setupTenantTemplate();
        $tenant = $this->setupDevTenant($domain, $host, $database);

        if (! $tenant) {
            $this->error('Failed to create dev tenant.');

            return Command::FAILURE;
        }

        $this->setupAdminUser($tenant);

        if (! $this->option('skip-workos') && ! $this->option('no-interaction')) {
            $this->handleWorkosIntegration();
        }

        $this->printSummary($landlordDb, $domain, $host, $database);

        return Command::SUCCESS;
    }

    private function checkPostgresConnection(): void
    {
        $this->info('Checking PostgreSQL connection...');

        try {
            DB::connection('landlord')->getPdo();
            $this->info('  PostgreSQL connection OK');
        } catch (\Exception $e) {
            $this->error('Could not connect to PostgreSQL: '.$e->getMessage());
            throw $e;
        }
    }

    private function checkRedisConnection(): void
    {
        $this->info('Checking Redis connection...');

        try {
            Redis::ping();
            $this->info('  Redis connection OK');
        } catch (\Exception $e) {
            $this->error('Could not connect to Redis: '.$e->getMessage());
            throw $e;
        }
    }

    private function handleFresh(string $tenantDatabase, string $landlordDb): bool
    {
        if (! $this->option('no-interaction')) {
            if (! confirm('This will DROP all dev databases and recreate them. Continue?', false)) {
                $this->info('Cancelled.');

                return false;
            }
        }

        $this->warn('Dropping databases for fresh setup...');

        if ($this->databaseExists($tenantDatabase)) {
            $this->dropDatabase($tenantDatabase);
            $this->info("  Dropped {$tenantDatabase}");
        }

        if ($this->databaseExists($this->templateDatabase)) {
            // Unmark as template before dropping
            try {
                DB::connection('landlord')->statement(
                    "ALTER DATABASE \"{$this->templateDatabase}\" IS_TEMPLATE false"
                );
            } catch (\Exception) {
                // May not be marked as template yet
            }
            $this->dropDatabase($this->templateDatabase);
            $this->info("  Dropped {$this->templateDatabase}");
        }

        // Wipe landlord tables instead of dropping the whole database
        if ($this->databaseExists($landlordDb)) {
            $this->info("  Wiping landlord tables in {$landlordDb}...");
            $tables = DB::connection('landlord')->select(
                "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
            );
            foreach ($tables as $table) {
                DB::connection('landlord')->statement(
                    "DROP TABLE IF EXISTS \"{$table->tablename}\" CASCADE"
                );
            }
            $this->info("  Wiped landlord tables in {$landlordDb}");
        }

        return true;
    }

    private function setupLandlordDatabase(string $landlordDb): void
    {
        if ($this->databaseExists($landlordDb)) {
            $this->info("Landlord database '{$landlordDb}' already exists - skipping");

            return;
        }

        $this->info("Creating landlord database: {$landlordDb}");
        $this->createDatabase($landlordDb);
        $this->info("  Created {$landlordDb}");
    }

    private function runLandlordMigrations(): void
    {
        $this->info('Running landlord migrations...');
        $this->call('migrate', [
            '--database' => 'landlord',
            '--path' => 'database/migrations/landlord',
            '--force' => true,
        ]);
    }

    private function setupTenantTemplate(): void
    {
        if (! $this->databaseExists($this->templateDatabase)) {
            $this->info("Creating tenant template database: {$this->templateDatabase}");
            $this->createDatabase($this->templateDatabase);
        } else {
            $this->info("Tenant template database '{$this->templateDatabase}' already exists - skipping creation");
        }

        // Ensure template record exists in landlord tenants table
        $templateExists = DB::connection('landlord')
            ->table('tenants')
            ->where('database', $this->templateDatabase)
            ->exists();

        if (! $templateExists) {
            DB::connection('landlord')->table('tenants')->insert([
                'id' => str()->uuid(),
                'name' => 'Tenant Template',
                'database' => $this->templateDatabase,
                'host' => 'tenant_template',
                'domain' => 'tenant_template.localhost',
                'org_id' => 'tenant_template_org',
            ]);
            $this->info('  Inserted tenant_template record');
        }

        // Run tenant migrations on the template
        $this->configureTenantConnection($this->templateDatabase);

        $this->info('Running tenant migrations on template...');
        $this->call('migrate', [
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // Seed EUR currency
        $this->info('Seeding EUR currency in template DB: '.$this->templateDatabase);
        $tenant = Tenant::where('database', $this->templateDatabase)->first();
        $tenant->makeCurrent();

        if (! Currency::where('code', 'EUR')->exists()) {
            Currency::create([
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'symbol_first' => true,
                'is_default' => true,
                'exchange_rate' => 1,
            ]);
            $this->info('  Seeded EUR currency in template');
        } else {
            $this->info('  EUR currency already exists in template - skipping');
        }

        // Seed roles for "tenant" guard
        $this->info('Seeding roles in template DB: '.$this->templateDatabase);
        foreach (RoleEnum::values() as $roleName) {
            if (! Role::where('name', $roleName)->where('guard_name', 'tenant')->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'tenant']);
                $this->info("  Created role: {$roleName}");
            } else {
                $this->info("  Role '{$roleName}' already exists - skipping");
            }
        }

        Tenant::forgetCurrent();

        // Mark as PostgreSQL template
        try {
            $this->terminateConnections($this->templateDatabase);
            DB::connection('landlord')->statement(
                "ALTER DATABASE \"{$this->templateDatabase}\" IS_TEMPLATE true"
            );
            $this->info("  Marked {$this->templateDatabase} as IS_TEMPLATE");
        } catch (\Exception $e) {
            // Already marked or other non-fatal issue
            $this->warn('  Could not mark as template: '.$e->getMessage());
        }
    }

    private function setupDevTenant(string $domain, string $host, string $database): ?Tenant
    {
        $this->info("Setting up dev tenant: {$domain}");

        // Check if tenant record already exists
        $existing = Tenant::where('domain', $domain)->first();
        if ($existing) {
            $this->info("  Dev tenant '{$domain}' already exists - skipping");

            return $existing;
        }

        // Create the tenant database from template
        if (! $this->databaseExists($database)) {
            $this->duplicateFromTemplate($database);
            $this->info("  Created database {$database} from template");
        } else {
            $this->info("  Database {$database} already exists - skipping duplication");
        }

        // Insert tenant record
        $tenant = Tenant::create([
            'name' => 'Dev Tenant',
            'domain' => $domain,
            'host' => $host,
            'database' => $database,
            'org_id' => 'dev_org',
        ]);

        $this->info("  Created tenant record for {$domain}");

        return $tenant;
    }

    private function setupAdminUser(Tenant $tenant): void
    {
        $email = $this->option('admin-email');
        $password = $this->option('admin-password');
        $name = $this->option('admin-name');

        $this->info("Setting up admin user: {$email}");

        $tenant->makeCurrent();
        $this->configureTenantConnection($tenant->database);

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->info("  Admin user '{$email}' already exists - skipping");
            Tenant::forgetCurrent();

            return;
        }

        // Create roles
        foreach (RoleEnum::values() as $roleName) {
            $this->info("  Ensuring role exists: {$roleName}");
            // Display current tenant
            $this->info("    Current tenant: {$tenant->domain} ({$tenant->database})");
            if (! Role::where('name', $roleName)->where('guard_name', 'tenant')->exists()) {
                $role = Role::create(['name' => $roleName, 'guard_name' => 'tenant']);
                $this->info("    Created role: {$roleName}");
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $nameParts = explode(' ', $name, 2);

        // Bypass model events to avoid assignRole('user') firing before the user is persisted
        $this->info('  Creating admin user and assigning roles...');
        $user = User::withoutEvents(function () use ($nameParts, $name, $email, $password): User {
            return User::create([
                'first_name' => $nameParts[0] ?? $name,
                'last_name' => $nameParts[1] ?? '',
                'handle' => str()->slug($name, '_'),
                'email' => $email,
                'password' => $password,
                'email_verified_at' => now(),
            ]);
        });

        $user->assignRole([RoleEnum::User, RoleEnum::Admin, RoleEnum::BusinessOwner]);
        $this->info('  Created admin user with Admin + BusinessOwner roles');

        Tenant::forgetCurrent();
    }

    private function handleWorkosIntegration(): void
    {
        $apiKey = config('services.workos.secret') ?? config('workos.api_key');
        $clientId = config('services.workos.client_id') ?? config('workos.client_id');

        if (! $apiKey || ! $clientId) {
            $this->info('WorkOS not configured - skipping integration');

            return;
        }

        $choice = select(
            label: 'WorkOS Integration',
            options: [
                'skip' => 'Skip WorkOS setup',
                'connect' => 'Connect existing WorkOS organization',
                'create' => 'Create new WorkOS organization',
            ],
            default: 'skip'
        );

        if ($choice === 'skip') {
            return;
        }

        try {
            WorkOS::setApiKey($apiKey);
            WorkOS::setClientId($clientId);

            if ($choice === 'connect') {
                $orgId = text(label: 'Enter WorkOS Organization ID', placeholder: 'org_...');
                $this->info("  Connected WorkOS org: {$orgId}");
            } elseif ($choice === 'create') {
                $orgName = text(label: 'Organization name for WorkOS', placeholder: 'My Organization');
                $organizations = new Organizations;
                $org = $organizations->createOrganization(name: $orgName);
                $this->info("  Created WorkOS org: {$org->id}");
            }
        } catch (\Exception $e) {
            $this->warn('WorkOS integration failed: '.$e->getMessage());
            $this->info('You can configure WorkOS manually later.');
        }
    }

    private function printSummary(string $landlordDb, string $domain, string $host, string $database): void
    {
        $this->newLine();
        $this->info('=== Dev Setup Complete ===');
        $this->newLine();
        $this->table(
            ['Resource', 'Value'],
            [
                ['Landlord DB', $landlordDb],
                ['Template DB', $this->templateDatabase],
                ['Tenant DB', $database],
                ['Domain', $domain],
                ['Host', $host],
                ['Admin Email', $this->option('admin-email')],
            ]
        );
        $this->newLine();
    }
}
