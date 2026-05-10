<?php

namespace App\Console\Commands\Crud;

use App\Models\Task;

class TaskDeleteCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'task:delete 
                            {id : The task ID}
                            {--force : Force delete (permanent delete)}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Delete a task';

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

        $task = Task::find($id);

        if (! $task) {
            $this->error("❌ Task with ID {$id} not found.");

            return self::FAILURE;
        }

        $this->warn("⚠️  About to delete task: {$task->title}");

        if ($force) {
            $this->warn('🔥 FORCE DELETE: This will permanently remove the task and cannot be undone!');
        }

        if (! $this->confirm('Are you sure you want to proceed?')) {
            $this->info('❌ Operation cancelled.');

            return self::SUCCESS;
        }

        try {
            if ($force) {
                $task->forceDelete();
                $this->info("🗑️  Task permanently deleted: {$task->title}");
            } else {
                $task->delete();
                $this->info("🗑️  Task deleted: {$task->title}");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to delete task: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
