<?php

namespace App\Domain\Project;

use App\Models\Organization;
use App\Models\Project;
use App\Models\Rate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

const SPECIFICITY_USER = 1; // 1 = most specific in a sortBy closure
const SPECIFICITY_ORGANIZATION = 2;
const SPECIFICITY_DEFAULT = 3;

class ProjectCost
{
    public Project $project;

    public ?Organization $organization;

    public ?Collection $users;

    public Collection $entries;

    public ?Collection $rates;

    public int $currentCost;

    public function __construct(Project $project, ?Collection $rates = null)
    {
        $this->project = $project->loadMissing('organization', 'users', 'dailyLogs.user', 'dailyLogs.clockEntries');
        $this->organization = $project->organization;
        $this->users = $project->users;
        $this->entries = $project->dailyLogs->pluck('clockEntries')->flatten();
        $this->rates = $rates;
    }

    public function calculateCost($cutoffDate = null): int
    {
        $cost = 0;
        $cutoffDate = $cutoffDate ? Date::make($cutoffDate) : now();

        $selectedEntries = $this->entries->where('out', '<=', $cutoffDate);

        foreach ($selectedEntries as $entry) {
            if (! $entry->duration_seconds) {
                continue;
            }

            $entryUser = $this->project->dailyLogs->firstWhere(function ($log) use ($entry) {
                return $log->id === $entry->daily_log_id;
            })->user;

            $duration = $this->durationToHours($entry->duration_seconds);
            $effectiveRate = $this->getRateBySpecificity(
                $entryUser->id,
                $this->organization?->id
            );

            if ($effectiveRate) {
                $cost += $effectiveRate->amount * $duration;
            }
        }

        return $cost;
    }

    private function roundDurationToNearestMinute(int $seconds): int
    {
        return round($seconds / 60) * 60;
    }

    private function durationToHours(int $seconds): float
    {
        $duration = $this->roundDurationToNearestMinute($seconds);

        return $duration / 3600;
    }

    /**
     * Get the most specific rate for a project based on user and organization.
     * Returns user-specific rate first, then organization-specific, then default (project-wide).
     */
    private function getRateBySpecificity(int $userId, ?int $organizationId): ?Rate
    {
        return $this->rates
            ->sortBy(function ($rate) use ($userId, $organizationId) {

                if ($rate->user_id === $userId) {
                    return SPECIFICITY_USER;
                }

                if ($rate->organization_id === $organizationId) {
                    return SPECIFICITY_ORGANIZATION;
                }

                return SPECIFICITY_DEFAULT;
            })->first();
    }
}
