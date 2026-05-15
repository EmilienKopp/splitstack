<?php

namespace App\Models;

use App\Domain\Shared\BaseEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseModel extends Model
{
    /**
     * @return TEntity
     *
     * @template TEntity of BaseEntity
     */
    public function toEntity(): object
    {
        $resource = JsonResource::make($this)->resolve();

        return static::entityClass()::fromArray($resource);
    }

    abstract public function entityClass(): string;
}
