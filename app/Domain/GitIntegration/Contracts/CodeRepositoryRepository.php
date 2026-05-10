<?php

namespace App\Domain\GitIntegration\Contracts;

use App\Domain\GitIntegration\Entities\CodeRepositoryEntity;

interface CodeRepositoryRepository
{
    public function findById(int $id): ?CodeRepositoryEntity;

    /** @return CodeRepositoryEntity[] */
    public function findByUserId(int $userId): array;

    public function save(CodeRepositoryEntity $repository): CodeRepositoryEntity;
}
