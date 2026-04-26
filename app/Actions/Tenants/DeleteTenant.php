<?php

namespace App\Actions\Tenants;

use App\Domain\Entities\Landlord\TenantEntity;
use App\Infrastructure\Contracts\TenantRepositoryInterface;

final class DeleteTenant
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenantRepository,
    ) {}

    public function handle(TenantEntity $tenant): void
    {
        // $this->tenantRepository->purge($tenant);
    }
}
