<?php

namespace App\Application\Actions\Tenants;

use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\Landlord\Contracts\TenantRepository;
use App\Domain\Landlord\Entities\TenantEntity;

final class CreateTenant
{
    public function __construct(
        private readonly TenantRepository $tenantRepository,
    ) {}

    public function handle(CreateTenantDTO $data): TenantEntity
    {
        $tenant = TenantEntity::fromNameAndSlug($data->org_name, $data->org_slug, $data->org_id);

        return $this->tenantRepository->save($tenant);
    }
}
