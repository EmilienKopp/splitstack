<?php

namespace App\Application\TimeTracking\Actions;

use App\Application\TimeTracking\DTOs\ClockOutDTO;
use App\Domain\TimeTracking\Contracts\ClockEntryRepository;
use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Exceptions\NoActiveClockEntry;

class ClockOut
{
    public function __construct(
        public readonly ClockEntryRepository $clockEntryRepository,
        public readonly DailyLogRepository $dailyLogRepository,
    ) {}

    public function execute(ClockOutDTO $data)
    {
        $log = $this->dailyLogRepository->findByUserDateAndProject(
            userId: $data->user_id,
            date: $data->out ?? now(),
            projectId: $data->project_id
        );

        if (! $log) {
            throw new NoActiveClockEntry;
        }

        $log->clockOut($data->out ?? now());
        $this->dailyLogRepository->save($log);

        return $log;
    }
}
