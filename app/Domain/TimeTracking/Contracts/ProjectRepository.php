<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\TimeTracking\Entities\ProjectEntity;
use Illuminate\Support\Collection;

interface ProjectRepository
{
    public function find(int $id): ?ProjectEntity;

    /** @return ProjectEntity[] */
    public function findByOrganizationId(int $organizationId): Collection;

    public function findForUser(int|string $userId): iterable;

    public function save(ProjectEntity $project): ProjectEntity;

    public function attachUser(int $projectId, int $userId): void;
}
