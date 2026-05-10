<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\TimeTracking\Contracts\ActivityLogRepository;
use App\Domain\TimeTracking\Entities\ActivityLogEntity;
use App\Models\ActivityLog;
use App\Models\DailyLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentActivityLogRepository implements ActivityLogRepository
{
    public function find(int $id): ?ActivityLogEntity
    {
        return ActivityLog::find($id)?->toEntity();
    }

    public function all(): Collection
    {
        return ActivityLog::toEntityCollection(ActivityLog::all());
    }

    public function findByClockEntry(int $clockEntryId): Collection
    {
        return ActivityLog::toEntityCollection(ActivityLog::where('clock_entry_id', $clockEntryId)->get());
    }

    public function findByUserAndDate(int $userId, string $date): Collection
    {
        $date = Carbon::parse($date);

        return DailyLog::where('user_id', $userId)
            ->daily($date)
            ->first()?->activities()->get() ?? collect();
    }

    public function sync(array $validated): void
    {
        DB::transaction(function () use ($validated) {
            $dailyLogId = $validated['daily_log_id'] ?? null;
            for ($i = 0; $i < count($validated['activities']); $i++) {
                $activity = $validated['activities'][$i];
                $hours = $activity['hours'] ?? 0;
                $minutes = $activity['minutes'] ?? 0;
                $activityLogId = $activity['id'] ?? null;
                if ($hours === 0 && $minutes === 0) {
                    continue;
                }

                $totalMinutes = ($hours * 60) + $minutes;
                $start_offset_seconds = 0; // Not handling offset properly yet, to come in future versions
                $end_offset_seconds = $start_offset_seconds + ($totalMinutes * 60);

                $data = [
                    'start_offset_seconds' => $start_offset_seconds,
                    'end_offset_seconds' => $end_offset_seconds,
                    'activity_type_id' => $activity['activity_type_id'] ?? null,
                    'daily_log_id' => $dailyLogId,
                ];

                if ($activityLogId) {
                    $data['id'] = $activityLogId;
                }
                ActivityLog::upsert($data, ['id'], [
                    'start_offset_seconds',
                    'end_offset_seconds',
                    'activity_type_id',
                    'daily_log_id',
                ]);
            }

            if (! empty($validated['deleted'])) {
                ActivityLog::whereIn('id', $validated['deleted'])->delete();
            }
        });
    }

    public function create(array $data): ActivityLogEntity
    {
        return ActivityLog::create($data)->toEntity();
    }

    public function update(int $id, array $data): ?ActivityLogEntity
    {
        $activityLog = ActivityLog::find($id);
        if (! $activityLog) {
            return null;
        }
        $activityLog->update($data);

        return $activityLog->fresh()->toEntity();
    }

    public function delete(int $id): bool
    {
        $activityLog = ActivityLog::find($id);
        if (! $activityLog) {
            return false;
        }

        return $activityLog->delete();
    }
}
