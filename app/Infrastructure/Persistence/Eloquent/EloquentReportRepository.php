<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\Report;
use App\Repositories\ReportRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function find(int $id): ?Report
    {
        return Report::find($id);
    }

    public function all(): Collection
    {
        return Report::all();
    }

    public function findByProject(int $projectId): Collection
    {
        return Report::where('project_id', $projectId)->get();
    }

    public function create(array $data): Report
    {
        return Report::create($data);
    }

    public function update(int $id, array $data): ?Report
    {
        $report = Report::find($id);
        if (! $report) {
            return null;
        }
        $report->update($data);

        return $report->fresh();
    }

    public function delete(int $id): bool
    {
        $report = Report::find($id);
        if (! $report) {
            return false;
        }

        return $report->delete();
    }
}
