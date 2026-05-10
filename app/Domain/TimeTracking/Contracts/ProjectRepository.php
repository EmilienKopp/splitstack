<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\TimeTracking\Entities\ProjectEntity;

interface ProjectRepository
{
    public function findById(int $id): ?ProjectEntity;

    /** @return ProjectEntity[] */
    public function findByOrganizationId(int $organizationId): array;

    public function save(ProjectEntity $project): ProjectEntity;
}
