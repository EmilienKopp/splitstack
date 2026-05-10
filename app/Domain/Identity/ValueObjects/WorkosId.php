<?php

namespace App\Domain\Identity\ValueObjects;

class WorkosId
{
    public function __construct(public readonly string $id)
    {
        if (empty($this->id)) {
            throw new \InvalidArgumentException('WorkOS ID cannot be empty.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }
}
