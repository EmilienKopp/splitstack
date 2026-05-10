<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Shared\ValueObjects\ID;
use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Entities\DailyLogEntity;
use App\Models\ClockEntry;
use App\Models\DailyLog;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EloquentDailyLogRepository implements DailyLogRepository
{
    public function findByDate(Carbon|string $date, ?ID $userId, ?ID $projectId): ?DailyLogEntity
    {
        return DailyLog::where('date', $date->format('Y-m-d'))
            ->where('user_id', $userId ?? Auth::guard('tenant')->id())
            ->when($projectId, function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->firstOrFail()
            ->toEntity();
    }

    public function find(ID $id): ?DailyLogEntity
    {
        return DailyLog::findOrFail($id)->toEntity();
    }

    public function findByUserAndDate(ID $userId, Carbon|DateTimeInterface $date): Collection
    {
        $collection = DailyLog::where('user_id', $userId)
            ->where('date', $date->format('Y-m-d'))
            ->get()
            ->map(fn (DailyLog $dailyLog) => $dailyLog->toEntity());

        return DailyLog::toEntityCollection($collection);
    }

    public function findByUserDateAndProject(ID $userId, Carbon|DateTimeInterface $date, ID $projectId): ?DailyLogEntity
    {
        $dailyLog = DailyLog::where('user_id', $userId)
            ->where('date', $date->format('Y-m-d'))
            ->where('project_id', $projectId)
            ->first();

        return $dailyLog?->toEntity();
    }

    public function save(DailyLogEntity $log): DailyLogEntity
    {
        return DB::transaction(function () use ($log) {
            $dailyLog = DailyLog::updateOrCreate(
                [
                    'id' => $log->id,
                ],
                [
                    'user_id' => $log->user_id,
                    'project_id' => $log->project_id,
                    'date' => $log->date->format('Y-m-d'),
                    'total_seconds' => $log->totalSeconds(),
                ])->toEntity();

            foreach ($log->clockEntries() as $entry) {
                ClockEntry::updateOrCreate(
                    ['id' => $entry->id],
                    $entry->toArray()
                );
            }

            return $dailyLog;
        });
    }
}
