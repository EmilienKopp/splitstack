<?php

namespace App\Domain\Organization\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class TeamMemberRemoved
{
    use Dispatchable;

    public function __construct(
        public int $teamId,
        public int $userId,
    ) {}
}
