<?php

namespace App\Domain\Finance\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via RateRepository.
 * Polymorphic scope: can belong to organization, project, or user (nullable FKs).
 */
class RateEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $rate_type_id,
        public readonly ?int $organization_id,
        public readonly ?int $project_id,
        public readonly ?int $user_id,
        public readonly string $rate_frequency,
        public readonly ?string $rate_type,
        public readonly float $amount,
        public readonly string $currency,
        public readonly float $overtime_multiplier,
        public readonly float $holiday_multiplier,
        public readonly float $special_multiplier,
        public readonly ?float $custom_multiplier_rate,
        public readonly ?string $custom_multiplier_label,
        public readonly bool $is_default,
        public readonly ?DateTimeInterface $effective_from,
        public readonly ?DateTimeInterface $effective_until,
    ) {}

    public function isActiveAt(DateTimeInterface $date): bool
    {
        if ($this->effective_from !== null && $date < $this->effective_from) {
            return false;
        }

        return $this->effective_until === null || $date <= $this->effective_until;
    }
}
