<?php

namespace App\UseCases\Organization;

use App\Domain\Entities\OrganizationEntity;
use App\Infrastructure\Contracts\OrganizationRepositoryInterface;

class CreateOrganization
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $organization,
    ) {}

    public function execute(array $data): OrganizationEntity
    {
        $entity = OrganizationEntity::fromArray($data);

        return $this->organization->save($entity);
    }
}
