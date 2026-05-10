<?php

namespace App\Domain\Shared\ValueObjects;

use DateTimeInterface;

class DateRange
{
    public function __construct(
        public readonly DateTimeInterface $start,
        public readonly ?DateTimeInterface $end = null,
    ) {
        if ($end !== null && $end < $start) {
            throw new \InvalidArgumentException('End date cannot be before start date.');
        }
    }

    public function contains(DateTimeInterface $date): bool
    {
        if ($date < $this->start) {
            return false;
        }

        return $this->end === null || $date <= $this->end;
    }

    public function equals(self $other): bool
    {
        return $this->start == $other->start && $this->end == $other->end;
    }
}
