<?php

namespace App\Domain\Organization\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class TeamMemberAdded
{
    use Dispatchable;

    public function __construct(
        public int $teamId,
        public int $userId,
        public string $role,
    ) {}
}
