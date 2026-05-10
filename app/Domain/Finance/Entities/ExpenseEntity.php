<?php

namespace App\Domain\Finance\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via ExpenseRepository.
 */
class ExpenseEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly int $budget_id,
        public readonly int $user_id,
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?string $description,
        public readonly string $status,
        public readonly DateTimeInterface $expense_date,
    ) {}
}
