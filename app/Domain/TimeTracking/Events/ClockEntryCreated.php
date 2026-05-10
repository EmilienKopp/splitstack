<?php

namespace App\Domain\TimeTracking\Events;

use Illuminate\Foundation\Bus\Dispatchable;

readonly class ClockEntryCreated
{
    use Dispatchable;

    public function __construct(
        public int $clockEntryId,
        public int $dailyLogId,
        public int $userId,
    ) {}
}
