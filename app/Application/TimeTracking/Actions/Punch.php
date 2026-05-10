<?php

namespace App\Application\TimeTracking\Actions;

use App\Domain\Shared\ValueObjects\ID;
use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use App\Domain\TimeTracking\Entities\DailyLogEntity;

class Punch
{
    public function __construct(
        protected readonly DailyLogRepository $dailyLogRepository,
    ) {}

    public function execute(DailyLogEntity|ID $dailyLog): DailyLogEntity
    {
        if ($dailyLog instanceof ID) {
            $dailyLog = $this->dailyLogRepository->find(id: $dailyLog);
        }

        $dailyLog->punch();

        $this->dailyLogRepository->save($dailyLog);

        return $dailyLog;
    }
}
