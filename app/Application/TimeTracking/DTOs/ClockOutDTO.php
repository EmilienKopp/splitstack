<?php

namespace App\Application\TimeTracking\DTOs;

use App\Application\Shared\Contracts\BaseDTO;
use App\Domain\Shared\ValueObjects\ID;
use Carbon\Carbon;

class ClockOutDTO extends BaseDTO
{
    public function __construct(
        public private(set) ID $user_id,
        public private(set) ID $project_id,
        public private(set) ?string $timezone = null,
        public private(set) ?Carbon $out = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            user_id: new ID($data['user_id']),
            project_id: new ID($data['project_id']),
            timezone: $data['timezone'] ?? null,
            out: isset($data['out']) ? new Carbon($data['out']) : null,
        );
    }
}
