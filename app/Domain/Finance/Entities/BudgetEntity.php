<?php

namespace App\Domain\Finance\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via BudgetRepository.
 * Owns BudgetAdjustmentEntity.
 * Enforces: effective amount = amount + sum of adjustment amounts.
 */
class BudgetEntity extends BaseEntity
{
    /** @var BudgetAdjustmentEntity[] */
    private array $adjustments = [];

    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly float $amount,
        public readonly ?float $amount_low,
        public readonly ?float $amount_high,
        public readonly string $currency,
        public readonly string $type,
        public readonly string $status,
        public readonly ?int $allocated_hours,
        public readonly DateTimeInterface $start_date,
        public readonly ?DateTimeInterface $end_date,
    ) {}

    public function addAdjustment(BudgetAdjustmentEntity $adjustment): void
    {
        $this->adjustments[] = $adjustment;
    }

    public function effectiveAmount(): float
    {
        return $this->amount + (float) array_sum(
            array_map(fn(BudgetAdjustmentEntity $a) => $a->adjustment_amount, $this->adjustments)
        );
    }

    /** @return BudgetAdjustmentEntity[] */
    public function adjustments(): array
    {
        return $this->adjustments;
    }
}
