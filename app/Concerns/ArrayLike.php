<?php

namespace App\Concerns;

trait ArrayLike
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->{$offset} ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->{$offset});
    }
}
