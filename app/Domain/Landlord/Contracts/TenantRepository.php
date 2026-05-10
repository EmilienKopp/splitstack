<?php

namespace App\Domain\Landlord\Contracts;

use App\Domain\Landlord\Entities\TenantEntity;
use Illuminate\Support\Collection;

interface TenantRepository
{
    public function findById(int|string $id): ?TenantEntity;

    public function findBySpace(string $space): ?TenantEntity;

    public function save(TenantEntity $tenant): TenantEntity;

    public function delete(int|string $id): void;

    /** @return Collection<TenantEntity> */
    public function all(): Collection;
}
