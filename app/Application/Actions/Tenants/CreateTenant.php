<?php

declare(strict_types=1);

namespace App\Application\Actions\Tenants;

use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\Entities\Landlord\TenantEntity;
use App\Infrastructure\Contracts\TenantRepositoryInterface;

final readonly class CreateTenant
{
    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
    ) {}

    public function handle(CreateTenantDTO $data): TenantEntity
    {
        $tenant = TenantEntity::fromNameAndSlug($data->org_name, $data->org_slug, $data->org_id);

        return $this->tenantRepository->save($tenant);
    }
}
