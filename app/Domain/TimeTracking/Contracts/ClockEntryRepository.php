<?php

namespace App\Domain\TimeTracking\Contracts;

use App\Domain\TimeTracking\Entities\ClockEntryEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface ClockEntryRepository
{
    public function find(int $id): ?ClockEntryEntity;

    public function all(): Collection;

    public function findByUser(int $userId): Collection;

    public function findActiveByUser(int $userId): ?ClockEntryEntity;

    public function getEntriesForUser(int $userId, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection;

    public function getTodayEntries(int $userId): Collection;

    public function save(ClockEntryEntity $data): ClockEntryEntity;

    public function delete(int $id): bool;

    // TODO: Move outside of Repository concerns and to UseCases or Business Logic
    // public function clockInOrOut(int $userId, int $projectId, ?DateTimeInterface $time = null, string $timezone = 'UTC'): ClockEntryEntity;

    // public function clockIn(int $userId, int $projectId, ?DateTimeInterface $clockInTime = null, ?string $timezone = null): ClockEntryEntity;

    // public function clockOut(int $clockEntryId, DateTimeInterface|string|null $time = null): ?ClockEntryEntity;

    // public function clockOutProject(int $userId, int $projectId, DateTimeInterface|string|null $clockOutTime = null): ?ClockEntryEntity;

    // public function updateClockOut(int $clockEntryId, array $data): ClockEntryEntity;

    // /**
    //  * Sync a batch of complete clock entries from the CLI.
    //  *
    //  * @param  array<int, array{client_id: ?string, repo_path: string, in: string, out: string, timezone: string}>  $entries
    //  * @return array{synced: list<string>, failed: list<array{client_id: ?string, reason: string}>}
    //  */
    // public function syncEntries(int $userId, array $entries): array;

    // public function closeAllToday(): void;
}
