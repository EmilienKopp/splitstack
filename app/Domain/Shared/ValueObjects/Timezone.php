<?php

namespace App\Domain\Shared\ValueObjects;

class Timezone
{
    public function __construct(public readonly string $value)
    {
        if (! \in_array($value, \DateTimeZone::listIdentifiers(), true)) {
            throw new \InvalidArgumentException("Invalid timezone: {$value}.");
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
