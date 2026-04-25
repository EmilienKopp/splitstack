<?php

namespace App\UseCases\Organization;

use App\Domain\Entities\OrganizationEntity;
use App\Infrastructure\Contracts\OrganizationRepositoryInterface;
use Illuminate\Support\Collection;

class ListOrganizations
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $organization,
    ) {}

    /** @return Collection<OrganizationEntity> */
    public function execute(): Collection
    {
        return $this->organization->all();
    }
}
