<?php

namespace App\Application\TimeTracking\Actions;

use App\Application\TimeTracking\DTOs\CreateProjectDTO;
use App\Domain\TimeTracking\Contracts\ProjectRepository;
use App\Domain\TimeTracking\Entities\ProjectEntity;
use Carbon\Carbon;

class CreateProject
{
    public function __construct(
        protected readonly ProjectRepository $projectRepository,
    ) {}

    public function execute(CreateProjectDTO $data): ProjectEntity
    {
        $project = new ProjectEntity(
            name: $data->name,
            status: $data->status,
            type: $data->type,
            description: $data->description,
            organization_id: $data->organization_id,
            start_date: $data->start_date ? Carbon::parse($data->start_date) : null,
        );

        $saved = $this->projectRepository->save($project);
        $this->projectRepository->attachUser($saved->id, $data->user_id);

        return $saved;
    }
}
