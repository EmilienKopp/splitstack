<?php

namespace App\Domain\Organization\Contracts;

use App\Domain\Organization\Entities\OrganizationEntity;

interface OrganizationRepository
{
    public function findById(int $id): ?OrganizationEntity;

    /** @return OrganizationEntity[] */
    public function findByUserId(int $userId): array;

    public function save(OrganizationEntity $organization): OrganizationEntity;
}
