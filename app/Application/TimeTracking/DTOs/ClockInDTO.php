<?php

namespace App\Application\TimeTracking\DTOs;

use App\Application\Shared\Contracts\BaseDTO;

class ClockInDTO extends BaseDTO
{
    public function __construct(
        public private(set) int $user_id,
        public private(set) int $project_id,
        public private(set) ?string $timezone = null,
        public private(set) ?string $in = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            user_id: $data['user_id'],
            project_id: $data['project_id'],
            timezone: $data['timezone'] ?? null,
            in: $data['in'] ?? null,
        );
    }
}
