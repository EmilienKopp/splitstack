<?php

namespace App\UseCases;

use App\Actions\Tenants\CreateTenant;
use App\Actions\Tenants\DeleteTenant;
use App\Actions\Users\CreateUser;
use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\DTOs\UserDTO;

final class RegisterBothTenantAndUser
{
    public function __construct(
        private readonly CreateTenant $createTenant,
        private readonly DeleteTenant $deleteTenant,
        private readonly CreateUser $createUser,
    ) {}

    public function execute(CreateTenantDTO $tenantData, UserDTO $userData): array
    {
        $tenant = $this->createTenant->handle($tenantData);

        try {
            $user = $this->createUser->handle($userData);
        } catch (\Throwable $e) {
            $this->deleteTenant->handle($tenant);
            throw $e;
        }

        return [
            'tenant' => $tenant,
            'user' => $user,
        ];
    }
}
