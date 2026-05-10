<?php

namespace App\Domain\Finance\Contracts;

use App\Domain\Finance\Entities\ExpenseEntity;

interface ExpenseRepository
{
    public function findById(int $id): ?ExpenseEntity;

    /** @return ExpenseEntity[] */
    public function findByProjectId(int $projectId): array;

    public function save(ExpenseEntity $expense): ExpenseEntity;
}
