<?php

namespace App\Application\Actions\Users;

use App\Domain\DTOs\UserDTO;
use App\Domain\Entities\UserEntity;
use App\Infrastructure\Contracts\UserRepositoryInterface;

final class CreateUser
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(UserDTO $data): UserEntity
    {
        $user = UserEntity::fromAuthInfo(
            name: $data->name,
            email: $data->email,
            password: $data->password,
            workos_id: $data->workos_id,
            org_id: $data->org_id,
            avatar: $data->avatar,
        );

        return $this->userRepository->save($user);
    }
}
