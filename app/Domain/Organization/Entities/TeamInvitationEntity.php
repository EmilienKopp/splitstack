<?php

namespace App\Domain\Organization\Entities;

use App\Domain\Shared\BaseEntity;
use DateTimeInterface;

/**
 * Child entity of Team — no standalone repository.
 */
class TeamInvitationEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $code,
        public readonly int $team_id,
        public readonly string $email,
        public readonly string $role,
        public readonly int $invited_by,
        public readonly ?DateTimeInterface $expires_at,
        public readonly ?DateTimeInterface $accepted_at,
    ) {}

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at < new DateTimeInterface();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }
}
