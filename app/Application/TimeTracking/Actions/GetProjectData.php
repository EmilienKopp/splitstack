<?php

namespace App\Application\TimeTracking\Actions;

use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Contracts\ProjectRepository;
use App\Domain\TimeTracking\Entities\ProjectEntity;

class GetProjectData
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly DailyLogRepository $dailyLogRepository,
    ) {}

    public function execute(int|string $id): ProjectEntity
    {
        $project = $this->projectRepository->getProjectData($id);
        if (! $project) {
            abort(404, 'Project not found');
        }

        return $project;
    }
}
