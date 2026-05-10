<?php

namespace App\Domain\Monitoring\Contracts;

use App\Models\MonitoredActivity;
use Illuminate\Support\Collection;

interface MonitoredActivityRepository
{
    public function find(int $id): ?MonitoredActivity;

    public function all(): Collection;

    public function create(array $data): MonitoredActivity;

    public function update(int $id, array $data): ?MonitoredActivity;

    public function delete(int $id): bool;
}
