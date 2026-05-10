<?php

namespace App\Domain\Identity\Contracts;

use App\Domain\Identity\Entities\UserEntity;
use Illuminate\Support\Collection;

interface UserRepository
{
    public function find(int|string $id, $with = []): ?UserEntity;

    public function findByWorkosId(int|string $workosId): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function save(UserEntity $user): UserEntity;

    /** @return Collection<UserEntity> */
    public function all(): Collection;

    public function delete(int|string $id): bool;
}
