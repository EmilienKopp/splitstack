<?php

namespace App\Domain\Identity\Contracts;

use App\Domain\Identity\Entities\UserEntity;
use Illuminate\Support\Collection;

interface UserRepository
{
    public function find(int $id): ?UserEntity;

    public function findByWorkosId(string $workosId): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function save(UserEntity $user): UserEntity;

    /** @return Collection<UserEntity> */
    public function all(): Collection;

    public function delete(int $id): bool;
}
