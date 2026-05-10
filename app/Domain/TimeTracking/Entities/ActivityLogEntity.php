<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of DailyLog — no standalone repository.
 */
class ActivityLogEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $daily_log_id,
        public readonly ?int $activity_type_id,
        public readonly ?int $task_id,
        public readonly ?int $expense_id,
        public readonly ?int $start_offset_seconds,
        public readonly ?int $end_offset_seconds,
        public readonly ?int $duration_seconds,
        public readonly ?string $notes,
    ) {}
}
