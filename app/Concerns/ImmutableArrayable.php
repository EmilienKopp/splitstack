<?php

namespace App\Concerns;

use LogicException;

trait ImmutableArrayable
{
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset} ?? null;
    }

    public function offsetSet($_offset, $_value): void
    {
        throw new LogicException('This object is immutable and cannot be modified.');
    }

    public function offsetUnset($_offset): void
    {
        throw new LogicException('This object is immutable and cannot be modified.');
    }
}
