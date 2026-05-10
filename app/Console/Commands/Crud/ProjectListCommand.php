<?php

namespace App\Console\Commands\Crud;

use App\Models\Project;

class ProjectListCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:list 
                            {--status= : Filter by status}
                            {--organization= : Filter by organization ID}
                            {--limit=10 : Number of projects to display}
                            {--userId= : Filter by user ID}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'List projects within the current tenant';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        $status = $this->option('status');
        $organizationId = $this->option('organization');
        $limit = (int) $this->option('limit');

        $query = Project::with(['organization']);

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $projects = $query->limit($limit)
            ->whereHas('users', function ($q) {
                $userId = $this->option('userId') ?? auth()->id();
                $q->where('users.id', $userId);
            })
            ->get();

        if ($projects->isEmpty()) {
            $this->warn('📭 No projects found.');

            return self::SUCCESS;
        }

        $this->info("📋 Found {$projects->count()} project(s):");

        $tableData = $projects->map(function ($project) {
            return [
                'ID' => $project->id,
                'Name' => $project->name,
                'Status' => $project->status,
                'Type' => $project->type ?? 'N/A',
                'Organization' => $project->organization?->name ?? 'N/A',
                'Budget' => $project->budget ? '$'.number_format($project->budget, 2) : 'N/A',
                'Created' => $project->created_at?->format('Y-m-d H:i') ?? 'N/A',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Name', 'Status', 'Type', 'Organization', 'Budget', 'Created'],
            $tableData
        );

        return self::SUCCESS;
    }
}
