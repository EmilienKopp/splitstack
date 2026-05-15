<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Entities\DailyLogEntity;
use App\Models\ClockEntry;
use App\Models\DailyLog;
use Carbon\Carbon;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentDailyLogRepository implements DailyLogRepository
{
    public function findByDate(Carbon|string $date, int|string|null $userId, int|string|null $projectId): ?DailyLogEntity
    {
        return DailyLog::where('date', $date->format('Y-m-d'))
            ->where('user_id', $userId)
            ->when($projectId, function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->firstOrFail()
            ->toEntity();
    }

    public function find(int|string $id): ?DailyLogEntity
    {
        return DailyLog::findOrFail($id)->toEntity();
    }

    public function findByUserAndDate(int|string $userId, Carbon|DateTimeInterface $date): Collection
    {
        $collection = DailyLog::where('user_id', $userId)
            ->where('date', $date->format('Y-m-d'))
            ->with('clockEntries')
            ->get()
            ->map(fn (DailyLog $dailyLog) => $dailyLog->toEntity());

        return DailyLog::toEntityCollection($collection);
    }

    public function findByUserDateAndProject(int|string $userId, Carbon|DateTimeInterface $date, int|string $projectId): ?DailyLogEntity
    {
        $dailyLog = DailyLog::where('user_id', $userId)
            ->where('date', $date->format('Y-m-d'))
            ->where('project_id', $projectId)
            ->with('clockEntries')
            ->first();

        if (! $dailyLog) {
            return null;
        }

        $entity = $dailyLog->toEntity();
        foreach ($dailyLog->clockEntries as $entry) {
            $entity->addClockEntry($entry->toEntity());
        }

        return $entity;
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

    public function getForProject(int|string $projectId, ?DateInterval $interval = null): Collection
    {
        $query = DailyLog::where('project_id', $projectId)
            ->with('clockEntries');

        if ($interval) {
            $query->where('date', '>=', Carbon::now()->sub($interval)->format('Y-m-d'));
        }

        return $query->get()->pipe(fn ($c) => DailyLog::toEntityCollection($c));
    }
}
