<?php

namespace App\Domain\GitIntegration\Contracts;

use Illuminate\Support\Collection;

interface GitLogRepository
{
    public function find(int $id): ?GitLogEntity;

    public function all(): Collection;

    public function create(array $data): GitLogEntity;

    public function update(int $id, array $data): ?GitLogEntity;

    public function delete(int $id): bool;
}
