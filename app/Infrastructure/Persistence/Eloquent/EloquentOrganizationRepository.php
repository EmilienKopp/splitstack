<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Organization;
use App\Repositories\OrganizationRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentOrganizationRepository implements OrganizationRepositoryInterface
{
    public function find(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function all(): Collection
    {
        return Organization::all();
    }

    public function findByUser(int $userId): Collection
    {
        return Organization::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(int $id, array $data): ?Organization
    {
        $org = Organization::find($id);
        if (! $org) {
            return null;
        }
        $org->update($data);

        return $org->fresh();
    }

    public function delete(int $id): bool
    {
        $org = Organization::find($id);
        if (! $org) {
            return false;
        }

        return $org->delete();
    }
}
