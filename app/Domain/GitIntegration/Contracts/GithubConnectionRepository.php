<?php

namespace App\Domain\GitIntegration\Contracts;

use App\Domain\GitIntegration\Entities\GithubConnectionEntity;

interface GithubConnectionRepository
{
    public function findById(int $id): ?GithubConnectionEntity;

    public function findByUserId(int $userId): ?GithubConnectionEntity;

    public function save(GithubConnectionEntity $connection): GithubConnectionEntity;
}
