<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\TimeTracking\Entities\ActivityLogEntity;
use Illuminate\Support\Collection;

interface ActivityLogRepository
{
    public function find(int $id): ?ActivityLogEntity;

    public function all(): Collection;

    public function findByClockEntry(int $clockEntryId): Collection;

    public function findByUserAndDate(int $userId, string $date): Collection;

    public function sync(array $validated): void;

    public function create(array $data): ?ActivityLogEntity;

    public function update(int $id, array $data): ?ActivityLogEntity;

    public function delete(int $id): bool;
}
