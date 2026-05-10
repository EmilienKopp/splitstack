<?php

namespace App\Infrastructure\ReadModels;

use App\Models\Organization;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class ProjectActivitySummary
{
    public Project $project;

    public ?Organization $organization;

    public Collection $users;

    public Collection $entries;

    public Collection $activityLogs;

    public function __construct(Project $project)
    {
        $this->project = $project->load('organization', 'users', 'dailyLogs.activities.activityType');
        $this->organization = $project->organization;
        $this->users = $project->users;
        $this->entries = $project->entries;
    }

    public function getActivityLogs($cutoffDate = null): Collection
    {
        $cutoffDate = $cutoffDate ? Date::make($cutoffDate) : now();

        return $this->project->dailyLogs->pluck('activities')
            ->flatten()
            ->where('created_at', '<=', $cutoffDate);
    }
}
