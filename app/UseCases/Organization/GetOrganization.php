<?php

namespace App\UseCases\Organization;

use App\Domain\Entities\OrganizationEntity;
use App\Infrastructure\Contracts\OrganizationRepositoryInterface;

class GetOrganization
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $organization,
    ) {}

    public function execute(int $id): ?OrganizationEntity
    {
        return $this->organization->findById($id);
    }
}
