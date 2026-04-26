<?php

namespace App\Actions\Tenants;

use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\Entities\Landlord\TenantEntity;
use App\Infrastructure\Contracts\TenantRepositoryInterface;

final class CreateTenant
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenantRepository,
    ) {}

    public function handle(CreateTenantDTO $data): TenantEntity
    {
        $tenant = TenantEntity::fromNameAndSlug($data->org_name, $data->org_slug, $data->org_id);

        return $this->tenantRepository->save($tenant);
    }
}
