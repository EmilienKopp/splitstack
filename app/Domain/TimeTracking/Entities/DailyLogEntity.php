<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;
use App\Domain\Shared\ValueObjects\ID;
use App\Domain\TimeTracking\Exceptions\AlreadyClockedInException;
use App\Domain\TimeTracking\Exceptions\NoActiveClockEntry;
use Carbon\Carbon;
use DateTimeInterface;

/**
 * Parent entity — load via DailyLogRepository.
 * Owns ClockEntryEntity, ActivityLogEntity, MonitoredActivityEntity.
 * Enforces: total_seconds = sum of clock entry durations.
 */
class DailyLogEntity extends BaseEntity
{
    /** @var ClockEntryEntity[] */
    private array $clockEntries = [];

    /** @var ActivityLogEntity[] */
    private array $activityLogs = [];

    /** @var MonitoredActivityEntity[] */
    private array $monitoredActivities = [];

    public function __construct(
        public readonly ID $user_id,
        public readonly ID $project_id,
        public readonly DateTimeInterface $date,
        public readonly ?ID $id = null,
        private int $total_seconds = 0,
    ) {}

    public function addClockEntry(ClockEntryEntity $entry): void
    {
        $this->clockEntries[] = $entry;
        $this->recalculateTotal();
    }

    public function clockIn(DateTimeInterface $time, ?string $timezone = 'UTC'): void
    {
        if ($this->findActiveEntry()) {
            throw AlreadyClockedInException::forProject($this->project_id);
        }

        $entry = new ClockEntryEntity(
            daily_log_id: $this->id,
            in: Carbon::instance($time),
            timezone: $timezone,
        );

        $this->addClockEntry($entry);
    }

    public function clockOut(DateTimeInterface $time): void
    {
        $activeEntry = $this->findActiveEntry();

        if (! $activeEntry) {
            throw NoActiveClockEntry::forProject($this->project_id);
        }

        $activeEntry->clockOut(Carbon::instance($time));
        $this->recalculateTotal();
    }

    public function punch(): void
    {
        $activeEntry = $this->findActiveEntry();

        if ($activeEntry) {
            $activeEntry->autoClock();
        } else {
            $this->clockIn(Carbon::now());
        }

        $this->recalculateTotal();
    }

    public function addActivityLog(ActivityLogEntity $log): void
    {
        $this->activityLogs[] = $log;
    }

    public function addMonitoredActivity(MonitoredActivityEntity $activity): void
    {
        $this->monitoredActivities[] = $activity;
    }

    public function recalculateTotal(): void
    {
        $this->total_seconds = (int) array_sum(
            array_map(fn (ClockEntryEntity $e) => $e->duration_seconds ?? 0, $this->clockEntries)
        );
    }

    public function totalSeconds(): int
    {
        return $this->total_seconds;
    }

    /** @return ClockEntryEntity[] */
    public function clockEntries(): array
    {
        return $this->clockEntries;
    }

    /** @return ActivityLogEntity[] */
    public function activityLogs(): array
    {
        return $this->activityLogs;
    }

    /** @return MonitoredActivityEntity[] */
    public function monitoredActivities(): array
    {
        return $this->monitoredActivities;
    }

    private function findActiveEntry(): ?ClockEntryEntity
    {
        return collect($this->clockEntries)
            ->first(fn ($e) => is_null($e->out));
    }
}
