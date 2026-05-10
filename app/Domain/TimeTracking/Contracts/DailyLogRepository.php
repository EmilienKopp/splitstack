<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\TimeTracking\Entities\DailyLogEntity;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;

interface DailyLogRepository
{
    public function find(int|string $id): ?DailyLogEntity;

    public function findByDate(Carbon|string $date, int|string|null $userId, int|string|null $projectId): ?DailyLogEntity;

    /** @return Collection<DailyLogEntity> */
    public function findByUserAndDate(int|string $userId, DateTimeInterface|Carbon $date): Collection;

    public function findByUserDateAndProject(int|string $userId, DateTimeInterface|Carbon $date, int|string $projectId): ?DailyLogEntity;

    public function save(DailyLogEntity $dailyLog): DailyLogEntity;
}
