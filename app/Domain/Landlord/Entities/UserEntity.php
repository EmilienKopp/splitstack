<?php

namespace App\Domain\Landlord\Entities;

final class UserEntity
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {}
}
