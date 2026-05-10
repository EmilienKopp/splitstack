<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentProjectRepository implements ProjectRepositoryInterface
{
    public function find(int $id): ?Project
    {
        return Project::find($id);
    }

    public function all(): Collection
    {
        return Project::all();
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return Project::where('organization_id', $organizationId)->get();
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(int $id, array $data): ?Project
    {
        $project = Project::find($id);
        if (! $project) {
            return null;
        }
        $project->update($data);

        return $project->fresh();
    }

    public function delete(int $id): bool
    {
        $project = Project::find($id);
        if (! $project) {
            return false;
        }

        return $project->delete();
    }

    public function findForUser(\App\Models\User|string $user)
    {
        if ($user instanceof \App\Models\User) {
            $userId = $user->id;
        } else {
            $userId = $user;
        }

        return Project::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }

    public function own()
    {
        $user = request()->user();
        if (! $user) {
            return collect();
        }

        $projects = $user->projects
            ->load(['organization', 'users', 'dailyLogs.user', 'dailyLogs.clockEntries']);

        return $projects;
    }

    public function findByName(string $name): ?Project
    {
        return Project::where('name', 'ILIKE', $name)->first();
    }
}
