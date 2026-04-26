<?php

namespace App\Infrastructure\Contracts;

use App\Domain\Entities\UserEntity;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /** @return Collection<UserEntity> */
    public function all(): Collection;

    public function findById(int $id): ?UserEntity;

    public function save(UserEntity $entity): UserEntity;

    public function delete(int $id): void;
}
