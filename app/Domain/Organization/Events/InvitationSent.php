<?php

namespace App\Domain\Organization\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class InvitationSent
{
    use Dispatchable;

    public function __construct(
        public int $teamId,
        public string $email,
        public string $code,
    ) {}
}
