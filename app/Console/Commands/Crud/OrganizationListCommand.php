<?php

namespace App\Console\Commands\Crud;

use App\Models\Organization;

class OrganizationListCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'organization:list 
                            {--type= : Filter by organization type}
                            {--limit=10 : Number of organizations to display}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'List organizations within the current tenant';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        $type = $this->option('type');
        $limit = (int) $this->option('limit');

        $query = Organization::withCount(['projects', 'users']);

        // Apply filters
        if ($type) {
            $query->where('type', $type);
        }

        $organizations = $query->limit($limit)->orderBy('created_at', 'desc')->get();

        if ($organizations->isEmpty()) {
            $this->warn('📭 No organizations found.');

            return self::SUCCESS;
        }

        $this->info("🏢 Found {$organizations->count()} organization(s):");

        $tableData = $organizations->map(function ($org) {
            return [
                'ID' => $org->id,
                'Name' => $org->name,
                'Type' => $org->type ?? 'N/A',
                'Projects' => $org->projects_count ?? 0,
                'Users' => $org->users_count ?? 0,
                'Created' => $org->created_at?->format('Y-m-d H:i') ?? 'N/A',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Name', 'Type', 'Projects', 'Users', 'Created'],
            $tableData
        );

        return self::SUCCESS;
    }
}
