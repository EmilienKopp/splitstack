<?php

namespace App\Domain\GitIntegration\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Parent entity — load via GoogleConnectionRepository.
 */
class GoogleConnectionEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $user_id,
        public readonly string $google_user_id,
        public readonly string $email,
        public readonly ?string $access_token,
        public readonly ?string $refresh_token,
        public readonly ?DateTimeInterface $token_expires_at,
    ) {}

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at !== null && $this->token_expires_at < new DateTimeInterface();
    }
}
