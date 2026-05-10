<?php

namespace App\Domain\Organization\Contracts;

use App\Domain\Organization\Entities\TeamEntity;

interface TeamRepository
{
    public function findById(int $id): ?TeamEntity;

    public function findBySlug(string $slug): ?TeamEntity;

    public function save(TeamEntity $team): TeamEntity;
}
