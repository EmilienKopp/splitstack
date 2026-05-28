<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class KnownIssueStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'known_issue_status_history';

    protected $fillable = [
        'known_issue_id',
        'status',
        'status_category',
        'status_category_name',
        'changed_at',
        'duration_seconds',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the known issue that this status history belongs to.
     */
    public function knownIssue()
    {
        return $this->belongsTo(KnownIssue::class);
    }

    /**
     * Get the duration in a human-readable format.
     */
    public function getDurationHuman(): string
    {
        if (! $this->duration_seconds) {
            return 'Unknown';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $days = floor($hours / 24);

        if ($days > 0) {
            $hours %= 24;

            return sprintf('%sd %dh', $days, $hours);
        }

        if ($hours > 0) {
            return sprintf('%sh %sm', $hours, $minutes);
        }

        return $minutes.'m';

    }
}
