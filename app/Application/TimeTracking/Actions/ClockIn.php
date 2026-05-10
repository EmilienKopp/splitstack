<?php

namespace App\Application\TimeTracking\Actions;

use App\Application\TimeTracking\DTOs\ClockInDTO;
use App\Domain\TimeTracking\Contracts\ClockEntryRepository;
use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Entities\ClockEntryEntity;
use App\Domain\TimeTracking\Entities\DailyLogEntity;
use Carbon\Carbon;

class ClockIn
{
    public function __construct(
        public readonly ClockEntryRepository $clockEntryRepository,
        public readonly DailyLogRepository $dailyLogRepository,
    ) {}

    public function execute(ClockInDTO $data)
    {
        $clockInTime = $data->in instanceof Carbon
            ? $data->in
            : Carbon::now($data->timezone);
        $dailyLog = $this->dailyLogRepository->findByUserAndDate($data->user_id, $clockInTime);
        if (! $dailyLog) {
            $dailyLog = $this->dailyLogRepository->save(
                new DailyLogEntity(
                    user_id: $data->user_id,
                    date: $clockInTime,
                    project_id: $data->project_id,
                )
            );
        }

        return $this->clockEntryRepository->save(
            new ClockEntryEntity(
                daily_log_id: $dailyLog->id,
                in: $clockInTime,
                timezone: $data->timezone,
            )
        );
    }
}
