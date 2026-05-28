<?php

declare(strict_types=1);

namespace App\Application\Actions\Tenants;

use App\Domain\Entities\Landlord\TenantEntity;
use App\Infrastructure\Contracts\TenantRepositoryInterface;

final readonly class DeleteTenant
{
    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
    ) {}

    public function handle(TenantEntity $tenant): void
    {
        $this->tenantRepository->delete($tenant->id);
    }
}
