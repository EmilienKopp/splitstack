<?php

namespace App\Domain\DTOs;

use App\Application\Shared\Contracts\BaseDTO;
use App\Domain\Shared\ValueObjects\ID;

class UserDTO extends BaseDTO
{
    public function __construct(
        public readonly ?ID $id = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?ID $org_id = null,
        public readonly ?ID $workos_id = null,
        public readonly ?string $avatar = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            id: isset($data['id']) ? new ID($data['id']) : null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            org_id: isset($data['org_id']) ? new ID($data['org_id']) : null,
            workos_id: isset($data['workos_id']) ? new ID($data['workos_id']) : null,
            avatar: $data['avatar'] ?? null,
        );
    }
}
