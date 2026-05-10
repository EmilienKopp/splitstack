<?php

namespace App\Domain\Finance\Contracts;

use App\Domain\Finance\Entities\RateEntity;

interface RateRepository
{
    public function findById(int $id): ?RateEntity;

    /** @return RateEntity[] */
    public function findApplicable(?int $organizationId, ?int $projectId, ?int $userId): array;

    public function save(RateEntity $rate): RateEntity;
}
