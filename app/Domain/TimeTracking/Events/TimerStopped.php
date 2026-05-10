<?php

namespace App\Domain\TimeTracking\Events;

use DateTimeInterface;
use Illuminate\Foundation\Bus\Dispatchable;

class TimerStopped
{
    use Dispatchable;

    public function __construct(
        public int $dailyLogId,
        public int $clockEntryId,
        public DateTimeInterface $stoppedAt,
        public int $durationSeconds,
    ) {}
}
