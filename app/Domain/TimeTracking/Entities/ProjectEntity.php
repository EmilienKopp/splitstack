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
        public string $status,
        public string $name,
        public string $type,
        public ?int $id = null,
        public ?int $organization_id = null,
        public ?string $description = null,
        public ?DateTimeInterface $start_date = null,
        public ?DateTimeInterface $end_date = null,
        public ?string $location = null,
        public ?string $icon = null,
        public ?string $default_break_duration_seconds = null,
        public array $entries = [],
        public array $daily_logs = [],
        public ?array $metadata = [],
    ) {}
}
