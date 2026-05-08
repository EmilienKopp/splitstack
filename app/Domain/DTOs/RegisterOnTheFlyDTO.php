<?php

namespace App\Domain\DTOs;

use App\Application\Shared\Contracts\BaseDTO;

class RegisterOnTheFlyDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $first_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $name = null,
        public readonly ?string $workos_id = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $org_name = null,
        public readonly ?string $org_slug = null,
        public readonly ?string $org_id = null,
        public readonly ?string $avatar = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            first_name: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            org_name: $data['org_name'] ?? null,
            org_slug: $data['org_slug'] ?? null,
            org_id: $data['org_id'] ?? null,
            workos_id: $data['workos_id'] ?? null,
            avatar: $data['avatar'] ?? null,
        );
    }
}
