<?php

namespace App\Domain\Categorization\ValueObjects;

class Tag
{
    public function __construct(public readonly string $label)
    {
        if (empty($this->label)) {
            throw new \InvalidArgumentException('Tag label cannot be empty.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->label === $other->label;
    }
}
