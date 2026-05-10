<?php

namespace App\Application\TimeTracking\Actions;

use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Entities\DailyLogEntity;

class Punch
{
    public function __construct(
        protected readonly DailyLogRepository $dailyLogRepository,
    ) {}

    public function execute(DailyLogEntity|int|string $dailyLog): DailyLogEntity
    {
        if (! $dailyLog instanceof DailyLogEntity) {
            $dailyLog = $this->dailyLogRepository->find(id: $dailyLog);
        }

        $dailyLog->punch();

        $this->dailyLogRepository->save($dailyLog);

        return $dailyLog;
    }
}
