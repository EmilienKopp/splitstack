<?php

namespace App\Models;

use App\Domain\TimeTracking\Entities\DailyLogEntity;
use App\Models\Concerns\HasEntity;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * DailyLog View Model
 *
 * This model represents the daily_logs_view database view which aggregates
 * clock entries by user, project, and date.
 *
 * Data Structure:
 * - The view aggregates clock entries into daily summaries
 * - activities() fetches Activity records (time breakdown by task category)
 * - clockEntries() fetches ClockEntry records (actual clock in/out times)
 *
 * Usage:
 * - getDaily($date): Get all daily logs for a specific date with related data
 * - getMonthly($date): Get all daily logs for a month with related data
 *
 * Related Models:
 * - Activity: Stores the breakdown of time by task category for each day/project
 * - ClockEntry: Stores the actual clock in/out times
 * - TaskCategory: Categories for activities (e.g., "Development", "Meeting")
 */
class DailyLog extends Model
{
    use HasEntity, UsesTenantConnection;

    protected $table = 'daily_logs';

    protected $fillable = [
        'date',
        'user_id',
        'project_id',
        'total_seconds',
    ];

    protected $casts = [
        'date' => 'date',
        'total_seconds' => 'integer',
    ];

    public static function booted()
    {
        static::saving(function ($model) {
            $model->total_seconds = $model->clockEntries()->sum('duration_seconds');
        });
    }

    public function clockEntries()
    {
        return $this->hasMany(ClockEntry::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function monitoredActivities()
    {
        return $this->hasMany(MonitoredActivity::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDaily($query, ?DateTimeInterface $date = null)
    {
        return $query->whereDate('date', $date ?? Carbon::today());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function scopeMonthly($query, ?DateTimeInterface $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        return $query->whereBetween('date', [
            $date->copy()->startOfMonth()->format('Y-m-d'),
            $date->copy()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function scopeOwn($query)
    {
        return $query->where('user_id', request()->user()->id);
    }

    /**
     * Get daily logs for a specific date with related data
     */
    public static function getDaily(DateTimeInterface $date)
    {
        return static::daily($date)->own()->with(['activities.activityType', 'clockEntries', 'project'])->get();
    }

    /**
     * Get monthly logs for a specific date range
     */
    public static function getMonthly($date)
    {

        return static::own()
            ->monthly($date)
            ->with(['activities.activityType', 'clockEntries'])
            ->get();
    }

    public static function recomputeTotalSeconds($dailyLogId)
    {
        $sql = <<<'SQL'
          UPDATE daily_logs SET total_seconds = (
              SELECT COALESCE(SUM(duration_seconds), 0)
              FROM clock_entries
              WHERE daily_log_id = ?
          )
          WHERE id = ?
        SQL;
        DB::connection('tenant')->statement($sql, [$dailyLogId, $dailyLogId]);
    }

    public function toEntity(): DailyLogEntity
    {
        return new DailyLogEntity(
            user_id: $this->user_id,
            project_id: $this->project_id,
            date: Carbon::parse($this->date),
            id: $this->id,
            total_seconds: $this->total_seconds,
        );
    }
}
