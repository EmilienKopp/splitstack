<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Concerns\ArrayLike;
use App\Domain\Shared\Contracts\Entity;
use ArrayIterator;
use Traversable;

abstract class BaseEntity implements Entity
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
        $data = array_filter($data, fn ($key): bool => property_exists(static::class, $key), ARRAY_FILTER_USE_KEY);

        return new static(...$data);
    }

    final public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }
}
