<?php

namespace App\Domain\Organization\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class InvitationAccepted
{
    use Dispatchable;

    public function __construct(
        public int $teamId,
        public int $userId,
        public string $code,
    ) {}
}
