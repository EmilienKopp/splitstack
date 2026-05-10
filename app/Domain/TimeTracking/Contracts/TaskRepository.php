<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\TimeTracking\Entities\TaskEntity;

interface TaskRepository
{
    public function findById(int $id): ?TaskEntity;

    /** @return TaskEntity[] */
    public function findByProjectId(int $projectId): array;

    public function save(TaskEntity $task): TaskEntity;
}
