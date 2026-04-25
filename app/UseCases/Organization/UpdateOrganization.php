<?php

namespace App\UseCases\Organization;

use App\Domain\Entities\OrganizationEntity;
use App\Infrastructure\Contracts\OrganizationRepositoryInterface;

class UpdateOrganization
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $organization,
    ) {}

    public function execute(OrganizationEntity $entity, array $data): OrganizationEntity
    {
        // TODO: apply $data mutations to $entity

        return $this->organization->save($entity);
    }
}
