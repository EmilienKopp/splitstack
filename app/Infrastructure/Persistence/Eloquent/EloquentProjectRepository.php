<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\TimeTracking\Contracts\ProjectRepository;
use App\Domain\TimeTracking\Entities\ProjectEntity;
use App\Models\Project;
use Illuminate\Support\Collection;

class EloquentProjectRepository implements ProjectRepository
{
    public function find(int $id): ?ProjectEntity
    {
        return Project::find($id)?->toEntity();
    }

    public function all(): Collection
    {
        return Project::all()->map->toEntity();
    }

    public function findByOrganizationId(int $organizationId): Collection
    {
        return Project::where('organization_id', $organizationId)->get()->map->toEntity();
    }

    public function save(ProjectEntity $project): ProjectEntity
    {
        $data = collect($project->toArray())->reject(fn ($v, $k) => $k === 'id' && is_null($v))->all();

        return Project::create($data)->toEntity();
    }

    public function attachUser(int $projectId, int $userId): void
    {
        Project::findOrFail($projectId)->users()->syncWithoutDetaching([$userId]);
    }

    public function update(int $id, array $data): ?ProjectEntity
    {
        $project = Project::find($id);
        if (! $project) {
            return null;
        }
        $project->update($data);

        return $project->fresh()->toEntity();
    }

    public function delete(int $id): bool
    {
        $project = Project::find($id);
        if (! $project) {
            return false;
        }

        return $project->delete();
    }

    public function findForUser(int|string $userId): iterable
    {
        return Project::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
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
