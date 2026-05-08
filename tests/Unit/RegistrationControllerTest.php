<?php

use App\Actions\Tenants\CreateTenant;
use App\Actions\Tenants\DeleteTenant;
use App\Actions\Users\CreateUser;
use App\Domain\Entities\Landlord\TenantEntity;
use App\Domain\Entities\UserEntity;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Requests\RegisterOnTheFlyRequest;
use App\Infrastructure\Contracts\TenantRepositoryInterface;
use App\Infrastructure\Contracts\UserRepositoryInterface;
use App\UseCases\RegisterBothTenantAndUser;
use Illuminate\Support\Collection;

test('on the fly registration returns inertia location for inertia requests', function () {
    $tenantRepository = new class implements TenantRepositoryInterface
    {
        public function all(): Collection
        {
            return collect();
        }

        public function findById(int|string $id): ?TenantEntity
        {
            return null;
        }

        public function save(TenantEntity $entity): TenantEntity
        {
            return TenantEntity::fromArray([
                ...$entity->toArray(),
                'id' => 'tenant_1',
            ]);
        }

        public function delete(int|string $id): void {}

        public function purge(TenantEntity $entity): void {}
    };

    $userRepository = new class implements UserRepositoryInterface
    {
        public function all(): Collection
        {
            return collect();
        }

        public function findById(int $id): ?UserEntity
        {
            return null;
        }

        public function save(UserEntity $entity): UserEntity
        {
            return UserEntity::fromArray([
                ...$entity->toArray(),
                'id' => 1,
            ]);
        }

        public function delete(int $id): void {}
    };

    $useCase = new RegisterBothTenantAndUser(
        new CreateTenant($tenantRepository),
        new DeleteTenant($tenantRepository),
        new CreateUser($userRepository),
    );

    $controller = new RegistrationController($useCase);

    $request = new class extends RegisterOnTheFlyRequest
    {
        public function validated($key = null, $default = null): array
        {
            return [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'org_name' => 'Acme Team',
                'org_slug' => 'acme-team',
                'org_id' => 'org_123',
                'workos_id' => 'workos_123',
                'avatar' => 'https://example.com/avatar.png',
            ];
        }

        public function inertia(): bool
        {
            return true;
        }
    };

    $response = $controller->registerOnTheFly($request);

    expect($response->getStatusCode())->toBe(409)
        ->and($response->headers->get('X-Inertia-Location'))
        ->toBe(route('dashboard', ['space' => 'acme-team']));
});
