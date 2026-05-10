<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;
use App\Domain\Shared\ValueObjects\ID;
use Carbon\Carbon;

/**
 * Child entity of DailyLog — no standalone repository.
 * Load via DailyLogRepository::findById().
 * Holds a rate snapshot at time of entry — do not recalculate from Rate entity at read time.
 */
class ClockEntryEntity extends BaseEntity
{
    public function __construct(
        public readonly ID $daily_log_id,
        public readonly ?Carbon $in,
        public readonly ?string $timezone = 'UTC',
        public readonly ?int $duration_seconds = 0,
        public readonly ?Carbon $out = null,
        public readonly ?float $applied_rate = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $client_id = null,
        public readonly ?ID $rate_id = null,
        public readonly ?string $notes = null,
        public readonly ?ID $id = null,
    ) {}

    public function isClockedIn(): bool
    {
        return $this->in !== null && $this->out === null;
    }

    public function isClockedOut(): bool
    {
        return $this->out !== null;
    }

    public function clockIn(Carbon $time): void
    {
        $this->in = $time;
        $this->out = null;
    }

    public function clockOut(Carbon $time): void
    {
        $this->out = $time;
    }

    public function autoClock(): void
    {
        $now = Carbon::now();
        if ($this->in->isSameDay($now) === false) {
            $this->in = null;
            $this->out = null;

            return;
        }
        if ($this->isClockedIn()) {
            $this->clockOut(Carbon::now());
        } else {
            $this->clockIn(Carbon::now());
        }
    }
}
