<?php

namespace App\Domain\DTOs;

use App\Application\Shared\Contracts\BaseDTO;

class UserDTO extends BaseDTO
{
    public function __construct(
        public readonly int|string|null $id = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly int|string|null $org_id = null,
        public readonly ?string $workos_id = null,
        public readonly ?string $avatar = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            org_id: $data['org_id'] ?? null,
            workos_id: $data['workos_id'] ?? null,
            avatar: $data['avatar'] ?? null,
        );
    }
}
