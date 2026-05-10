<?php

namespace App\Domain\GitIntegration\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via GithubConnectionRepository.
 */
class GithubConnectionEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $user_id,
        public readonly string $github_user_id,
        public readonly string $username,
        public readonly ?string $account_handle,
        public readonly ?string $avatar_url,
        public readonly ?string $access_token,
        public readonly ?string $refresh_token,
        public readonly ?DateTimeInterface $token_expires_at,
    ) {}

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at !== null && $this->token_expires_at < new DateTimeInterface();
    }
}
