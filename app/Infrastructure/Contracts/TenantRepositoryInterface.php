<?php

namespace App\Infrastructure\Contracts;

use App\Domain\Entities\Landlord\TenantEntity;
use Illuminate\Support\Collection;

interface TenantRepositoryInterface
{
    /** @return Collection<TenantEntity> */
    public function all(): Collection;

    public function findById(int|string $id): ?TenantEntity;

    public function save(TenantEntity $entity): TenantEntity;

    public function delete(int|string $id): void;

    public function purge(TenantEntity $entity): void;
}
