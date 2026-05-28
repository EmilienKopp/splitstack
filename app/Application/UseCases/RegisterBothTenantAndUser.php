<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Actions\Tenants\CreateTenant;
use App\Application\Actions\Tenants\DeleteTenant;
use App\Application\Actions\Users\CreateUser;
use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\DTOs\UserDTO;
use Throwable;

final readonly class RegisterBothTenantAndUser
{
    public function __construct(
        private CreateTenant $createTenant,
        private DeleteTenant $deleteTenant,
        private CreateUser $createUser,
    ) {}

    public function execute(CreateTenantDTO $tenantData, UserDTO $userData): array
    {
        $tenant = $this->createTenant->handle($tenantData);

        try {
            $user = $this->createUser->handle($userData);
        } catch (Throwable $throwable) {
            $this->deleteTenant->handle($tenant);
            throw $throwable;
        }

        return [
            'tenant' => $tenant,
            'user' => $user,
        ];
    }
}
