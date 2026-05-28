<?php

declare(strict_types=1);

namespace App\Application\Actions\Users;

use App\Domain\DTOs\UserDTO;
use App\Domain\Entities\UserEntity;
use App\Infrastructure\Contracts\UserRepositoryInterface;

final readonly class CreateUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(UserDTO $data): UserEntity
    {
        $user = UserEntity::fromAuthInfo(
            name: $data->name,
            email: $data->email,
            password: $data->password,
            org_id: $data->org_id,
            workos_id: $data->workos_id,
            avatar: $data->avatar,
        );

        return $this->userRepository->save($user);
    }
}
