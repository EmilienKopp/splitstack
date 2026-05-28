<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Concerns\ArrayLike;
use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;
use Traversable;

abstract readonly class BaseValueObject implements Arrayable, ArrayAccess, IteratorAggregate
{
    use ArrayLike;

    public function __construct(...$data)
    {
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            $this->{$property} = $data[$property] ?? null;
        }
    }

    final public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    final public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }
}
