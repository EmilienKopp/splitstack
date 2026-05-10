<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\GitLog;
use App\Repositories\GitLogRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentGitLogRepository implements GitLogRepositoryInterface
{
    protected string $model = GitLog::class;

    public function find(int $id): ?GitLog
    {
        return GitLog::find($id);
    }

    public function all(): Collection
    {
        return GitLog::all();
    }

    public function create(array $data): GitLog
    {
        return GitLog::create($data);
    }

    public function update(int $id, array $data): ?GitLog
    {
        $gitLog = GitLog::find($id);
        if ($gitLog) {
            $gitLog->update($data);
        }

        return $gitLog;
    }

    public function delete(int $id): bool
    {
        $gitLog = GitLog::find($id);
        if ($gitLog) {
            return $gitLog->delete();
        }

        return false;
    }
}