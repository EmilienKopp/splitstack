<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Child entity of DailyLog — no standalone repository.
 */
class MonitoredActivityEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $daily_log_id,
        public readonly string $process,
        public readonly ?string $window_title,
        public readonly DateTimeInterface $timestamp,
    ) {}
}
