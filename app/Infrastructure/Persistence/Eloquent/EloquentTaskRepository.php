<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function find(int $id): ?Task
    {
        return Task::find($id);
    }

    public function all(): Collection
    {
        return Task::all();
    }

    public function findByProject(int $projectId): Collection
    {
        return Task::where('project_id', $projectId)->get();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): ?Task
    {
        $task = Task::find($id);
        if (! $task) {
            return null;
        }
        $task->update($data);

        return $task->fresh();
    }

    public function delete(int $id): bool
    {
        $task = Task::find($id);
        if (! $task) {
            return false;
        }

        return $task->delete();
    }
}
