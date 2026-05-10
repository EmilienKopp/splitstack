<?php

namespace App\Application\TimeTracking\DTOs;

use Carbon\Carbon;

final class CreateDailyLogDTO
{
    public function __construct(
        public private(set) int|string $user_id,
        public private(set) int|string $project_id,
        public private(set) Carbon $date,
        public private(set) int $total_seconds = 0,
    ) {}
}
