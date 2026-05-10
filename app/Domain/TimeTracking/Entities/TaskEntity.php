<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Parent entity — load via TaskRepository.
 */
class TaskEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $priority,
        public readonly bool $completed,
    ) {}
}
