<?php

namespace App\Application\TimeTracking\DTOs;

use App\Application\Shared\Contracts\BaseDTO;
use Carbon\Carbon;

class ClockOutDTO extends BaseDTO
{
    public function __construct(
        public private(set) int|string $user_id,
        public private(set) int|string $project_id,
        public private(set) ?string $timezone = null,
        public private(set) ?Carbon $out = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            user_id: $data['user_id'],
            project_id: $data['project_id'],
            timezone: $data['timezone'] ?? null,
            out: isset($data['out']) ? new Carbon($data['out']) : null,
        );
    }
}
