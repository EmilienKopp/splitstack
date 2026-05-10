<?php

namespace App\Domain\Shared;

use App\Concerns\ArrayLike;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

abstract class BaseEntity implements Arrayable, ArrayAccess, IteratorAggregate
{
    use ArrayLike;

    public function __construct(...$data)
    {
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            $this->{$property} = $data[$property] ?? null;
        }
    }

    public static function fromArray(array $data): static
    {
        // Protect from "Unknown named parameters" error if array keys don't match constructor params
        $data = array_filter($data, fn ($key) => property_exists(static::class, $key), ARRAY_FILTER_USE_KEY);

        return new static(...$data);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->toArray());
    }
}
