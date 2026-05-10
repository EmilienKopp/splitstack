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
        public readonly string $status,
        public readonly string $name,
        public readonly string $type,
        public readonly ?int $id = null,
        public readonly ?int $organization_id = null,
        public readonly ?string $description = null,
        public readonly ?DateTimeInterface $start_date = null,
        public readonly ?DateTimeInterface $end_date = null,
        public readonly ?string $location = null,
        public readonly ?string $icon = null,
        public readonly ?string $default_break_duration_seconds = null,
        public readonly ?array $metadata = [],
    ) {}
}
