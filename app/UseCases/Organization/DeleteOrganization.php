<?php

namespace App\UseCases\Organization;

use App\Infrastructure\Contracts\OrganizationRepositoryInterface;

class DeleteOrganization
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $organization,
    ) {}

    public function execute(int $id): void
    {
        $this->organization->delete($id);
    }
}
