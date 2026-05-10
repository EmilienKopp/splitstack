<?php

namespace App\Application\Actions\Tenants;

use App\Domain\Landlord\Contracts\TenantRepository;
use App\Domain\Landlord\Entities\TenantEntity;

final class DeleteTenant
{
    public function __construct(
        private readonly TenantRepository $tenantRepository,
    ) {}

    public function handle(TenantEntity $tenant): void
    {
        $this->tenantRepository->delete($tenant->id);
    }
}
