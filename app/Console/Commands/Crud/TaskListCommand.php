<?php

namespace App\Console\Commands\Crud;

use App\Models\Task;

class TaskListCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'task:list 
                            {--project= : Filter by project ID}
                            {--priority= : Filter by priority}
                            {--status= : Filter by status}
                            {--limit=10 : Number of tasks to display}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'List tasks within the current tenant';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        $projectId = $this->option('project');
        $priority = $this->option('priority');
        $status = $this->option('status');
        $limit = (int) $this->option('limit');

        $query = Task::with(['project']);

        // Apply filters
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tasks = $query->limit($limit)->orderBy('created_at', 'desc')->get();

        if ($tasks->isEmpty()) {
            $this->warn('📭 No tasks found.');

            return self::SUCCESS;
        }

        $this->info("📋 Found {$tasks->count()} task(s):");

        $tableData = $tasks->map(function ($task) {
            return [
                'ID' => $task->id,
                'Title' => substr($task->title ?? 'N/A', 0, 30).(strlen($task->title ?? '') > 30 ? '...' : ''),
                'Priority' => $task->priority ?? 'N/A',
                'Status' => $task->status ?? 'N/A',
                'Project' => $task->project?->name ?? 'N/A',
                'Due Date' => $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A',
                'Created' => $task->created_at?->format('Y-m-d H:i') ?? 'N/A',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Title', 'Priority', 'Status', 'Project', 'Due Date', 'Created'],
            $tableData
        );

        return self::SUCCESS;
    }
}
