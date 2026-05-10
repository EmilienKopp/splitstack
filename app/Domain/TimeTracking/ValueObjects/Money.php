<?php

namespace App\Domain\TimeTracking\ValueObjects;

class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException("Cannot add different currencies: {$this->currency} and {$other->currency}.");
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
