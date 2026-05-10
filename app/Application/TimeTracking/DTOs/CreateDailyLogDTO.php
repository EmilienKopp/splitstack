<?php

namespace App\Application\TimeTracking\DTOs;

use App\Domain\Shared\ValueObjects\ID;
use Carbon\Carbon;

final class CreateDailyLogDTO
{
    public function __construct(
        public private(set) ID $user_id,
        public private(set) ID $project_id,
        public private(set) Carbon $date,
        public private(set) int $total_seconds = 0,
    ) {}
}
