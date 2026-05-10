<?php

namespace App\Console\Commands\Crud;

use App\Models\Project;
use App\Models\Task;

class TaskCreateCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'task:create 
                            {title : The task title}
                            {--project= : Project ID to associate with}
                            {--description= : Task description}
                            {--priority=medium : Task priority (low, medium, high, urgent)}
                            {--status=pending : Task status}
                            {--due-date= : Due date (YYYY-MM-DD format)}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new task within the current tenant';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        if (! $this->validateArguments(['title'])) {
            return self::FAILURE;
        }

        $title = $this->argument('title');
        $projectId = $this->option('project');
        $description = $this->option('description');
        $priority = $this->option('priority');
        $status = $this->option('status');
        $dueDate = $this->option('due-date');

        // Validate project if provided
        if ($projectId) {
            $project = Project::find($projectId);
            if (! $project) {
                $this->error("❌ Project with ID {$projectId} not found.");

                return self::FAILURE;
            }
            $this->info("📋 Using project: {$project->name}");
        }

        // Validate priority
        if (! in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
            $this->error('❌ Invalid priority. Must be one of: low, medium, high, urgent');

            return self::FAILURE;
        }

        // Validate due date format if provided
        if ($dueDate) {
            try {
                $dueDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dueDate);
            } catch (\Exception $e) {
                $this->error('❌ Invalid due date format. Use YYYY-MM-DD (e.g., 2025-12-31)');

                return self::FAILURE;
            }
        }

        try {
            $taskData = [
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'status' => $status,
                'due_date' => $dueDate,
            ];

            if ($projectId) {
                $taskData['project_id'] = $projectId;
            }

            $task = Task::create($taskData);

            $this->showSuccess('Task created', $task);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create task: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
