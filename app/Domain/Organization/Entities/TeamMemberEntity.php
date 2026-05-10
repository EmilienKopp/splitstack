<?php

namespace App\Domain\Organization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of Team — no standalone repository.
 */
class TeamMemberEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $team_id,
        public readonly int $user_id,
        public readonly string $role,
    ) {}
}
