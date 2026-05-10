<?php

namespace App\Domain\TimeTracking\Events;

use DateTimeInterface;
use Illuminate\Foundation\Bus\Dispatchable;

class TimerStarted
{
    use Dispatchable;

    public function __construct(
        public int $dailyLogId,
        public int $userId,
        public DateTimeInterface $startedAt,
    ) {}
}
