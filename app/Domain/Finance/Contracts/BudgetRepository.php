<?php

namespace App\Domain\Finance\Contracts;

use App\Domain\Finance\Entities\BudgetEntity;

interface BudgetRepository
{
    public function findById(int $id): ?BudgetEntity;

    /** @return BudgetEntity[] */
    public function findByProjectId(int $projectId): array;

    public function save(BudgetEntity $budget): BudgetEntity;
}
