<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via ActivityRepository.
 */
class ActivityEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $user_id,
        public readonly int $project_id,
        public readonly int $task_category_id,
        public readonly DateTimeInterface $date,
        public readonly int $duration,
        public readonly ?string $notes,
    ) {}
}
