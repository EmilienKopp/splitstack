<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\TimeTracking\Contracts\ClockEntryRepository;
use App\Domain\TimeTracking\Entities\ClockEntryEntity;
use App\Models\ClockEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EloquentClockEntryRepository implements ClockEntryRepository
{
    protected static array $with = ['dailyLog.project'];

    public function find(int|string $id): ?ClockEntryEntity
    {
        $clockEntry = ClockEntry::with(static::$with)->find($id);

        return $clockEntry ? $clockEntry->toEntity() : null;
    }

    public function all(): Collection
    {
        return ClockEntry::with(static::$with)->get()->map->toEntity();
    }

    public function findByUser(int|string $userId): Collection
    {
        return ClockEntry::with(static::$with)->where('user_id', $userId)->get();
    }

    public function findActiveByUser(int|string $userId): ?ClockEntryEntity
    {
        $clockEntry = ClockEntry::whereHas('dailyLog', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->whereNull('out')
            ->today()
            ->first();

        return $clockEntry ? $clockEntry->toEntity() : null;
    }

    public function save(ClockEntryEntity $entry): ClockEntryEntity
    {
        if ($entry->id) {
            return $this->update($entry->id, $entry->toArray());
        }

        return $this->create($entry->toArray());
    }

    private function create(array $data): ClockEntryEntity
    {
        return ClockEntry::create($data)->toEntity();
    }

    private function update(int|string $id, array $data): ?ClockEntryEntity
    {
        $entry = ClockEntry::findOrFail($id);

        $entry->update($data);

        return $entry->fresh()->toEntity();
    }

    public function delete(int $id): bool
    {
        $entry = ClockEntry::findOrFail($id);

        return $entry->delete();
    }

    /**
     * Get all clock entries for a user, optionally filtered by date range
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEntriesForUser(
        int|string $userId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): Collection {
        $query = ClockEntry::where('user_id', $userId)
            ->with('project')
            ->orderBy('in', 'desc');

        if ($startDate) {
            $query->where('in', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('in', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Get today's clock entries for a user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayEntries(int|string $userId): Collection
    {
        return ClockEntry::whereHas('dailyLog', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereDate('in', now())
            ->with('dailyLog.project')
            ->orderBy('in', 'desc')
            ->get();
    }
}
