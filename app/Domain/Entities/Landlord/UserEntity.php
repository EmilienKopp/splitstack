<?php

declare(strict_types=1);

namespace App\Domain\Entities\Landlord;

final readonly class UserEntity
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}
}
