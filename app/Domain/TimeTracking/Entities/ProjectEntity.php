<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via ProjectRepository.
 */
class ProjectEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $organization_id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $type,
        public readonly string $status,
        public readonly ?DateTimeInterface $start_date,
        public readonly ?DateTimeInterface $end_date,
        public readonly ?string $location,
        public readonly ?string $icon,
        public readonly ?string $default_break_duration_seconds,
        public readonly ?array $metadata,
    ) {}
}
