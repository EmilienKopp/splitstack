<?php

namespace App\Domain\Organization\ValueObjects;

class MemberRole
{
    private const VALID = ['owner', 'admin', 'member', 'guest'];

    public function __construct(public readonly string $role)
    {
        if (! \in_array($role, self::VALID, true)) {
            throw new \InvalidArgumentException("Invalid member role: {$role}.");
        }
    }

    public function equals(self $other): bool
    {
        return $this->role === $other->role;
    }
}
