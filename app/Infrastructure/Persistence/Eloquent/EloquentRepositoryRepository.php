<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Repository;
use App\Repositories\RepositoryRepositoryInterface;

class EloquentRepositoryRepository implements RepositoryRepositoryInterface
{
    protected string $model = Repository::class;

    public function find(int|string $id): ?Repository
    {
        if (is_numeric($id)) {
            return Repository::find($id);
        }

        return Repository::where('url', $id)->orWhere('path', $id)->first();
    }

    public function all(): \Illuminate\Support\Collection
    {
        return Repository::all();
    }

    public function pluck(string $column, ?string $key = null): \Illuminate\Support\Collection
    {
        return Repository::pluck($column, $key);
    }

    public function findByProject(int $projectId): \Illuminate\Support\Collection
    {
        return Repository::where('project_id', $projectId)->get();
    }

    public function update(int $id, array $data): ?Repository
    {
        $repository = Repository::find($id);
        if ($repository) {
            $repository->update($data);
        }

        return $repository;
    }

    public function create(array $data): Repository
    {
        return Repository::create($data);
    }

    public function delete(int $id): bool
    {
        $repository = Repository::find($id);
        if ($repository) {
            return $repository->delete();
        }

        return false;
    }
}
