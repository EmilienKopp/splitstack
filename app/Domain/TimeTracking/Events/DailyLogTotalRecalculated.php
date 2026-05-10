<?php

namespace App\Domain\TimeTracking\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class DailyLogTotalRecalculated
{
    use Dispatchable;

    public function __construct(
        public int $dailyLogId,
        public int $newTotalSeconds,
    ) {}
}
