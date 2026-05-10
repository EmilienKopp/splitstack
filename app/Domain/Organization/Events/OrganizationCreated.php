<?php

namespace App\Domain\Organization\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class OrganizationCreated
{
    use Dispatchable;

    public function __construct(
        public int $organizationId,
        public int $userId,
        public string $name,
    ) {}
}
