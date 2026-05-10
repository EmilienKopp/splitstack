<?php

namespace App\Console\Commands\Crud;

use App\Models\Project;

class ProjectShowCommand extends BaseTenantAwareCrudCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'project:show 
                            {id : The project ID}
                            {--with-tasks : Include associated tasks}
                            {--with-users : Include associated users}
                            {--tenant=* : The tenant(s) to run this command for}';

    /**
     * The console command description.
     */
    protected $description = 'Show detailed information about a specific project';

    /**
     * Execute the command.
     */
    protected function executeCommand(): int
    {
        if (! $this->validateArguments(['id'])) {
            return self::FAILURE;
        }

        $id = $this->argument('id');
        $withTasks = $this->option('with-tasks');
        $withUsers = $this->option('with-users');

        $query = Project::with(['organization']);

        if ($withTasks) {
            $query->with('tasks');
        }

        if ($withUsers) {
            $query->with('users');
        }

        $project = $query->find($id);

        if (! $project) {
            $this->error("❌ Project with ID {$id} not found.");

            return self::FAILURE;
        }

        $this->displayModel($project, 'Project Details');

        if ($withTasks && $project->tasks->isNotEmpty()) {
            $this->info("\n📋 Associated Tasks:");
            $taskData = $project->tasks->map(function ($task) {
                return [
                    'ID' => $task->id,
                    'Title' => $task->title ?? $task->name ?? 'N/A',
                    'Priority' => $task->priority ?? 'N/A',
                    'Status' => $task->status ?? 'N/A',
                    'Created' => $task->created_at?->format('Y-m-d H:i') ?? 'N/A',
                ];
            })->toArray();

            $this->table(
                ['ID', 'Title', 'Priority', 'Status', 'Created'],
                $taskData
            );
        }

        if ($withUsers && $project->users->isNotEmpty()) {
            $this->info("\n👥 Associated Users:");
            $userData = $project->users->map(function ($user) {
                return [
                    'ID' => $user->id,
                    'Name' => $user->name,
                    'Email' => $user->email,
                    'Roles' => is_array($user->pivot->roles) ? implode(', ', $user->pivot->roles) : ($user->pivot->roles ?? 'N/A'),
                ];
            })->toArray();

            $this->table(
                ['ID', 'Name', 'Email', 'Roles'],
                $userData
            );
        }

        return self::SUCCESS;
    }
}
