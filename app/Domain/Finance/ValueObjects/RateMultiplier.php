<?php

namespace App\Domain\Finance\ValueObjects;

class RateMultiplier
{
    public function __construct(
        public readonly float $value,
        public readonly string $label,
    ) {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Rate multiplier must be positive.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value && $this->label === $other->label;
    }
}
