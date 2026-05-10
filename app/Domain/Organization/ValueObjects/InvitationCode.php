<?php

namespace App\Domain\Organization\ValueObjects;

class InvitationCode
{
    public function __construct(public readonly string $code)
    {
        if (empty($this->code)) {
            throw new \InvalidArgumentException('Invitation code cannot be empty.');
        }
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }
}
