<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use App\Domain\TimeTracking\Entities\ActivityLogEntity;
use App\Models\Concerns\HasEntity;
use Database\Factories\ActivityLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ActivityLog extends Model
{
    /** @use HasFactory<ActivityLogFactory> */
    use HasEntity, HasFactory, UsesTenantConnection;

    protected $fillable = [
        'clock_entry_id',
        'activity_type_id',
        'task_id',
        'start_offset_seconds',
        'end_offset_seconds',
        'notes',
    ];

    #[ExportRelationship(ClockEntry::class)]
    public function clockEntry()
    {
        return $this->belongsTo(ClockEntry::class);
    }

    #[ExportRelationship(ActivityType::class)]
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class);
    }

    #[ExportRelationship(Task::class)]
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    #[ExportRelationship(User::class)]
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toEntity(): ActivityLogEntity
    {
        return new ActivityLogEntity(
            id: $this->id,
            dailyLogId: $this->clockEntry?->dailyLog?->id ?? 0,
            activityTypeId: $this->activity_type_id,
            taskId: $this->task_id,
            expenseId: null, // Not handling expenses yet, to come in future versions
            startOffsetSeconds: $this->start_offset_seconds,
            endOffsetSeconds: $this->end_offset_seconds,
            durationSeconds: ($this->end_offset_seconds ?? 0) - ($this->start_offset_seconds ?? 0),
            notes: $this->notes,
        );
    }
}
