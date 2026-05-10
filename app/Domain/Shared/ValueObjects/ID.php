<?php

namespace App\Domain\Shared\ValueObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

final class ID implements Castable
{
    public function __construct(public readonly string|int $value)
    {
        if (! preg_match('/^[a-zA-Z0-9\-_]+$/', (string) $this->value)) {
            throw new \InvalidArgumentException("Invalid ID format: {$this->value}.");
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes): ?ID
            {
                return $value !== null ? new ID($value) : null;
            }

            public function set($model, string $key, $value, array $attributes): ?string
            {
                if ($value === null) {
                    return null;
                }

                return (string) $value;
            }
        };
    }
}
