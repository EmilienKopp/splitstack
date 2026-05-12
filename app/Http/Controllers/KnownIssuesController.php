<?php

namespace App\Http\Controllers;

use App\Models\Landlord\KnownIssue;
use Inertia\Inertia;

class KnownIssuesController extends Controller
{
    /**
     * Display the public known issues page.
     */
    public function index()
    {
        $issues = KnownIssue::orderBy('first_reported_at', 'desc')
            ->get()
            ->map(function ($issue) {
                return [
                    'id' => $issue->id,
                    'jira_key' => $issue->jira_key,
                    'summary' => $issue->summary,
                    'description' => $issue->description,
                    'status' => $issue->status,
                    'status_category' => $issue->status_category,
                    'status_category_name' => $issue->status_category_name,
                    'status_color' => $issue->status_color,
                    'priority' => $issue->priority,
                    'priority_icon_url' => $issue->priority_icon_url,
                    'issue_type' => $issue->issue_type,
                    'issue_type_icon_url' => $issue->issue_type_icon_url,
                    'first_reported_at' => $issue->first_reported_at?->toISOString(),
                    'first_reported_human' => $issue->getTimeSinceReportedHuman(),
                    'last_updated_at' => $issue->last_updated_at?->toISOString(),
                    'current_status_since' => $issue->current_status_since?->toISOString(),
                    'current_status_duration' => $issue->getCurrentStatusDurationInSeconds(),
                    'current_status_duration_human' => $issue->getCurrentStatusDurationHuman(),
                    'is_deployed' => $issue->is_deployed,
                    'deployed_at' => $issue->deployed_at?->toISOString(),
                    'deployment_metadata' => $issue->deployment_metadata,
                ];
            });

        return Inertia::render('KnownIssues/Index', [
            'issues' => $issues,
        ]);
    }
}
