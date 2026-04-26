<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\UserEntity;
use App\Infrastructure\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    /** @return Collection<UserEntity> */
    public function all(): Collection
    {
        return User::all()->map(
            fn (User $model) => UserEntity::fromArray($model->toArray()),
        );
    }

    public function findById(int $id): ?UserEntity
    {
        $model = User::find($id);

        return $model ? UserEntity::fromArray($model->toArray()) : null;
    }

    public function save(UserEntity $entity): UserEntity
    {
        if (! $entity instanceof UserEntity) {
            throw new \InvalidArgumentException('Expected UserEntity');
        }

        $data = array_filter($entity->toArray(), fn ($v) => $v !== null);

        $model = User::updateOrCreate(
            array_filter(['id' => $entity->id]),
            $data,
        );

        return UserEntity::fromArray($model->toArray());
    }

    public function delete(int $id): void
    {
        User::destroy($id);
    }
}
