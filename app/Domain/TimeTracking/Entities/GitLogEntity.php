<?php

namespace App\Domain\TimeTracking\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Child entity of DailyLog — no standalone repository.
 */
class GitLogEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $repository_id,
        public readonly int $user_id,
        public readonly int $daily_log_id,
        public readonly ?string $commit_hash,
        public readonly ?string $author_name,
        public readonly ?string $author_email,
        public readonly ?DateTimeInterface $committed_at,
        public readonly string $message,
        public readonly ?string $diff,
    ) {}
}
