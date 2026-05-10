<?php

namespace App\Domain\GitIntegration\Entities;

final class GitLogEntity
{
    public function __construct(
        public int $id,
        public string $repository_id,
        public string $commit_hash,
        public string $author_name,
        public string $author_email,
        public string $message,
        public string $diff,
        public int|string $user_id,
        public \DateTime $committed_at,
    ) {}
}
