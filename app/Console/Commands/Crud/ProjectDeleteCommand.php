<?php

namespace App\Console\Commands\Crud;

use App\Models\Project;

class ProjectDeleteCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:delete 
                            {id : The project ID}
                            {--force : Force delete (bypass soft delete)}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Delete a project (soft delete by default)';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        if (! $this->validateArguments(['id'])) {
            return self::FAILURE;
        }

        $id = $this->argument('id');
        $force = $this->option('force');

        $project = Project::find($id);

        if (! $project) {
            $this->error("❌ Project with ID {$id} not found.");

            return self::FAILURE;
        }

        $this->warn("⚠️  About to delete project: {$project->name}");

        if ($force) {
            $this->warn('🔥 FORCE DELETE: This will permanently remove the project and cannot be undone!');
        } else {
            $this->info('ℹ️  This will be a soft delete. The project can be restored later.');
        }

        if (! $this->confirm('Are you sure you want to proceed?')) {
            $this->info('❌ Operation cancelled.');

            return self::SUCCESS;
        }

        try {
            if ($force) {
                $project->forceDelete();
                $this->info("🗑️  Project permanently deleted: {$project->name}");
            } else {
                $project->delete();
                $this->info("🗑️  Project soft deleted: {$project->name}");
                $this->info("💡 Use 'project:restore {$id}' to restore it later.");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to delete project: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
