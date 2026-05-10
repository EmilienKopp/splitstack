<?php

namespace App\Domain\TimeTracking\ValueObjects;

use DateTimeInterface;

class ClockTimestamp
{
    public function __construct(
        public readonly DateTimeInterface $at,
        public readonly Timezone $tz,
    ) {}

    public function equals(self $other): bool
    {
        return $this->at == $other->at && $this->tz->equals($other->tz);
    }
}
