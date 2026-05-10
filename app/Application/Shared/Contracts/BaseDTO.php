<?php

namespace App\Application\Shared\Contracts;

use App\Concerns\ImmutableArrayable;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

abstract class BaseDTO implements DTO, IteratorAggregate
{
    use ImmutableArrayable;

    public static function fromValidatable(HasValidatedData $source): static
    {
        return static::fromArray($source->validated());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    abstract public static function fromArray(array $data): static;

    /**
     * @param  Arrayable<int|string, mixed>  $entity
     */
    public static function fromEntity(Arrayable $entity): static
    {
        return static::fromArray($entity->toArray());
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->toArray());
    }
}
