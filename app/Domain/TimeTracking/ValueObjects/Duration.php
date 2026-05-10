<?php

namespace App\Domain\TimeTracking\ValueObjects;

class Duration
{
    public function __construct(public readonly int $seconds)
    {
        if ($seconds < 0) {
            throw new \InvalidArgumentException('Duration cannot be negative.');
        }
    }

    public function toHours(): float
    {
        return $this->seconds / 3600;
    }

    public function add(self $other): self
    {
        return new self($this->seconds + $other->seconds);
    }

    public function equals(self $other): bool
    {
        return $this->seconds === $other->seconds;
    }
}
