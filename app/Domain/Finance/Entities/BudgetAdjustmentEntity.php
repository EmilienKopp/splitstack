<?php

namespace App\Domain\Finance\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of Budget — no standalone repository.
 * Load via BudgetRepository.
 */
class BudgetAdjustmentEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $budget_id,
        public readonly int $user_id,
        public readonly float $adjustment_amount,
        public readonly ?float $adjustment_amount_low,
        public readonly ?float $adjustment_amount_high,
        public readonly string $currency,
        public readonly ?string $reason,
    ) {}
}
