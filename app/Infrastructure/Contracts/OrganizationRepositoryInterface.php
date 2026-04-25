<?php

namespace App\Infrastructure\Contracts;

use App\Domain\Entities\OrganizationEntity;
use Illuminate\Support\Collection;

interface OrganizationRepositoryInterface
{
    /** @return Collection<OrganizationEntity> */
    public function all(): Collection;

    public function findById(int $id): ?OrganizationEntity;

    public function save(OrganizationEntity $entity): OrganizationEntity;

    public function delete(int $id): void;
}
