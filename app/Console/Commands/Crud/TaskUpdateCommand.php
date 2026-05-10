<?php

namespace App\Console\Commands\Crud;

use App\Models\Task;

class TaskUpdateCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'task:update 
                            {id : The task ID}
                            {--title= : Update task title}
                            {--description= : Update task description}
                            {--priority= : Update task priority}
                            {--status= : Update task status}
                            {--due-date= : Update due date (YYYY-MM-DD format)}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Update an existing task';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        if (! $this->validateArguments(['id'])) {
            return self::FAILURE;
        }

        $id = $this->argument('id');
        $task = Task::find($id);

        if (! $task) {
            $this->error("❌ Task with ID {$id} not found.");

            return self::FAILURE;
        }

        $this->info("📝 Updating task: {$task->title}");

        $updates = [];

        // Collect updates
        if ($title = $this->option('title')) {
            $updates['title'] = $title;
        }

        if ($description = $this->option('description')) {
            $updates['description'] = $description;
        }

        if ($priority = $this->option('priority')) {
            if (! in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
                $this->error('❌ Invalid priority. Must be one of: low, medium, high, urgent');

                return self::FAILURE;
            }
            $updates['priority'] = $priority;
        }

        if ($status = $this->option('status')) {
            $updates['status'] = $status;
        }

        if ($dueDate = $this->option('due-date')) {
            try {
                $updates['due_date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $dueDate);
            } catch (\Exception $e) {
                $this->error('❌ Invalid due date format. Use YYYY-MM-DD (e.g., 2025-12-31)');

                return self::FAILURE;
            }
        }

        if (empty($updates)) {
            $this->warn('⚠️  No updates provided. Use options like --title, --description, --priority, etc.');

            return self::SUCCESS;
        }

        try {
            $task->update($updates);
            $task->refresh();

            $this->showSuccess('Task updated', $task);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to update task: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
