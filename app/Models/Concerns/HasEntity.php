<?php

namespace App\Models\Concerns;

use Illuminate\Support\Collection;

trait HasEntity
{
    abstract public function toEntity();

    public static function toEntityCollection(iterable $models): Collection
    {
        return collect($models)->map(fn ($model) => $model->toEntity());
    }

    public static function toEntityArray(iterable $models): array
    {
        return static::toEntityCollection($models)->toArray();
    }
}
