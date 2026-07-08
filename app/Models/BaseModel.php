<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Shared\BaseEntity;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use TEntity;

abstract class BaseModel extends Model
{
    protected $dateFormat = 'Y-m-d H:i:sO';

    abstract public function entityClass(): string;

    /**
     * Create or update a model instance from an entity.
     *
     * @param  BaseEntity  $entity  The entity to convert to a model.
     * @param  string  $key  The entity property to use as the model's primary key.
     * @param  bool  $fetch  Whether to fetch the model from the database (true) or create a new instance (false). If false, the model will be marked as existing if the entity has a primary key.
     *
     * @template TEntity of BaseEntity
     *
     * @return TEntity
     */
    final public static function fromEntity(BaseEntity $entity, string $key = 'id', bool $fetch = true): object
    {
        if ($fetch) {
            $model = static::findOrNew($entity->$key);
        } else {
            $model = new static();
            $model->{$model->getKeyName()} = $entity->$key;
            $model->exists = $entity->$key !== null;
        }

        $model->fill(Arr::only($entity->toArray(), $model->getFillable()));

        return $model;
    }

    /**
     * @return TEntity
     *
     * @template TEntity of BaseEntity
     */
    final public function toEntity(): object
    {
        $resource = JsonResource::make($this)->resolve();

        return static::entityClass()::fromArray($resource);
    }

    #[Scope]
    protected function tzWhere($query, string $column, string $operator, $value)
    {
        return $query->whereRaw('DATE("'.$column.'" AT TIME ZONE ?) '.$operator.' ?', [timezone(), $value->toDateString()]);
    }
}
