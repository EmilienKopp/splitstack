<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\Shared\ValueObjects\ID;
use App\Domain\TimeTracking\Entities\DailyLogEntity;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;

interface DailyLogRepository
{
    public function find(ID $id): ?DailyLogEntity;

    public function findByDate(Carbon|string $date, ?ID $userId, ?ID $projectId): ?DailyLogEntity;

    /** @return Collection<DailyLogEntity> */
    public function findByUserAndDate(ID $userId, DateTimeInterface|Carbon $date): Collection;

    public function findByUserDateAndProject(ID $userId, DateTimeInterface|Carbon $date, ID $projectId): ?DailyLogEntity;

    public function save(DailyLogEntity $dailyLog): DailyLogEntity;
}
