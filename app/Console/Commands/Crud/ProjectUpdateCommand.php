<?php

namespace App\Console\Commands\Crud;

use App\Models\Project;

class ProjectUpdateCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:update 
                            {id : The project ID}
                            {--name= : Update project name}
                            {--description= : Update project description}
                            {--status= : Update project status}
                            {--type= : Update project type}
                            {--budget= : Update project budget}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Update an existing project';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        if (! $this->validateArguments(['id'])) {
            return self::FAILURE;
        }

        $id = $this->argument('id');
        $project = Project::find($id);

        if (! $project) {
            $this->error("❌ Project with ID {$id} not found.");

            return self::FAILURE;
        }

        $this->info("📝 Updating project: {$project->name}");

        $updates = [];

        // Collect updates
        if ($name = $this->option('name')) {
            $updates['name'] = $name;
        }

        if ($description = $this->option('description')) {
            $updates['description'] = $description;
        }

        if ($status = $this->option('status')) {
            if (! in_array($status, ['active', 'inactive', 'completed', 'on_hold'])) {
                $this->error('❌ Invalid status. Must be one of: active, inactive, completed, on_hold');

                return self::FAILURE;
            }
            $updates['status'] = $status;
        }

        if ($type = $this->option('type')) {
            if (! in_array($type, ['client', 'internal', 'personal'])) {
                $this->error('❌ Invalid type. Must be one of: client, internal, personal');

                return self::FAILURE;
            }
            $updates['type'] = $type;
        }

        if ($budget = $this->option('budget')) {
            $updates['budget'] = (float) $budget;
        }

        if (empty($updates)) {
            $this->warn('⚠️  No updates provided. Use options like --name, --description, --status, etc.');

            return self::SUCCESS;
        }

        try {
            $project->update($updates);
            $project->refresh();

            $this->showSuccess('Project updated', $project);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to update project: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
