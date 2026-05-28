<?php

declare(strict_types=1);

namespace App\Application\Shared\Contracts;

use App\Concerns\ImmutableArrayable;
use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;
use Traversable;

abstract class BaseDTO implements DTO, IteratorAggregate
{
    use ImmutableArrayable;

    /**
     * @param  array<string, mixed>  $data
     */
    abstract public static function fromArray(array $data): static;

    final public static function fromValidatable(HasValidatedData $source): static
    {
        return static::fromArray($source->validated());
    }

    /**
     * @param  Arrayable<int|string, mixed>  $entity
     */
    final public static function fromEntity(Arrayable $entity): static
    {
        return static::fromArray($entity->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    final public function toArray(): array
    {
        return get_object_vars($this);
    }

    final public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }
}
