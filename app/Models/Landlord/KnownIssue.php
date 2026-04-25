<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnownIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'jira_id',
        'jira_key',
        'summary',
        'description',
        'status',
        'status_category',
        'status_category_name',
        'status_color',
        'priority',
        'priority_icon_url',
        'issue_type',
        'issue_type_icon_url',
        'first_reported_at',
        'last_updated_at',
        'current_status_since',
        'metadata',
        'is_deployed',
        'deployment_metadata',
        'deployed_at',
    ];

    protected $casts = [
        'first_reported_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'current_status_since' => 'datetime',
        'metadata' => 'array',
        'is_deployed' => 'boolean',
        'deployment_metadata' => 'array',
        'deployed_at' => 'datetime',
    ];

    /**
     * Get the status history for this issue.
     */
    public function statusHistory()
    {
        return $this->hasMany(KnownIssueStatusHistory::class)->orderBy('changed_at', 'desc');
    }

    /**
     * Get the duration in the current status in seconds.
     */
    public function getCurrentStatusDurationInSeconds(): int
    {
        if (! $this->current_status_since) {
            return 0;
        }

        return now()->diffInSeconds($this->current_status_since);
    }

    /**
     * Get the duration in the current status in a human-readable format.
     */
    public function getCurrentStatusDurationHuman(): string
    {
        if (! $this->current_status_since) {
            return 'Unknown';
        }

        return $this->current_status_since->diffForHumans(null, true);
    }

    /**
     * Get the time since first reported in a human-readable format.
     */
    public function getTimeSinceReportedHuman(): string
    {
        if (! $this->first_reported_at) {
            return 'Unknown';
        }

        return $this->first_reported_at->diffForHumans();
    }

    /**
     * Check if the status has changed.
     */
    public function hasStatusChanged(string $newStatus): bool
    {
        return $this->status !== $newStatus;
    }

    /**
     * Update the status and record the change in history.
     */
    public function updateStatus(string $newStatus, ?string $statusCategory = null, ?string $statusCategoryName = null): void
    {
        if (! $this->hasStatusChanged($newStatus)) {
            return;
        }

        // Calculate duration in previous status
        $durationSeconds = null;
        if ($this->current_status_since) {
            $durationSeconds = now()->diffInSeconds($this->current_status_since);
        }

        // Record the old status in history
        if ($this->status) {
            KnownIssueStatusHistory::create([
                'known_issue_id' => $this->id,
                'status' => $this->status,
                'status_category' => $this->status_category,
                'status_category_name' => $this->status_category_name,
                'changed_at' => $this->current_status_since ?? now(),
                'duration_seconds' => abs(floor($durationSeconds)),
            ]);
        }

        // Update to new status
        $this->status = $newStatus;
        $this->status_category = $statusCategory ?? $this->status_category;
        $this->status_category_name = $statusCategoryName ?? $this->status_category_name;
        $this->current_status_since = now();
        $this->save();
    }
}
