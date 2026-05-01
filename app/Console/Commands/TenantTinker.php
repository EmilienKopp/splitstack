<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantTinker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:tinker {tenant : The tenant identifier to tinker with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a tinker session for a specific tenant. Mostly inspired by Spatie\'s tenants:artisan command but more flexible in tenant selection (case insensitive, partial match)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantIdentifier = $this->argument('tenant');

        if (blank($tenantIdentifier)) {
            $this->error('Please provide a tenant identifier as an argument.');

            return -1;
        }

        $tenant = Tenant::query()
            ->where(function ($q) use ($tenantIdentifier) {
                $q->where('database', 'ilike', "%{$tenantIdentifier}%")
                    ->orWhere('space', 'ilike', "%{$tenantIdentifier}%")
                    ->orWhere('domain', 'ilike', "%{$tenantIdentifier}%");
            })
            ->first();

        if (! $tenant) {
            $this->error("No tenant found matching: {$tenantIdentifier}");

            return -1;
        }

        $tenant->execute(function () use ($tenant) {
            $this->line('');
            $this->info("Running tinker for tenant `{$tenant->name}` (id: {$tenant->getKey()}, database: {$tenant->database})...");
            $this->line('---------------------------------------------------------');

            Artisan::call('tinker', [], $this->output);
        });

        return 0;
    }
}
