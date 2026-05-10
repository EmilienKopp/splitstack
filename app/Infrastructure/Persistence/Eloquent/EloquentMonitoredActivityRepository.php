<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\MonitoredActivity;
use App\Repositories\MonitoredActivityRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentMonitoredActivityRepository implements MonitoredActivityRepositoryInterface
{
    protected string $model = MonitoredActivity::class;

    public function find(int $id): ?MonitoredActivity
    {
        return MonitoredActivity::find($id);
    }

    public function all(): Collection
    {
        return MonitoredActivity::all();
    }

    public function create(array $data): MonitoredActivity
    {
        return MonitoredActivity::create($data);
    }

    public function update(int $id, array $data): ?MonitoredActivity
    {
        $monitoredActivity = MonitoredActivity::find($id);
        if ($monitoredActivity) {
            $monitoredActivity->update($data);
        }

        return $monitoredActivity;
    }

    public function delete(int $id): bool
    {
        $monitoredActivity = MonitoredActivity::find($id);
        if ($monitoredActivity) {
            return $monitoredActivity->delete();
        }

        return false;
    }
}
