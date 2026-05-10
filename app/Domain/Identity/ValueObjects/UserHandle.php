<?php

namespace App\Domain\Identity\ValueObjects;

class UserHandle
{
    public function __construct(public readonly string $handle)
    {
        if (! preg_match('/^[a-z0-9_\-]{2,32}$/', $this->handle)) {
            throw new \InvalidArgumentException("Invalid user handle: {$this->handle}.");
        }
    }

    public function equals(self $other): bool
    {
        return $this->handle === $other->handle;
    }
}
