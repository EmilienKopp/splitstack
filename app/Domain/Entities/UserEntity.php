<?php

declare(strict_types=1);

namespace App\Domain\Entities;

final readonly class UserEntity
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $org_id = null,
        public ?string $workos_id = null,
        public ?string $avatar = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromAuthInfo($name, $email, $password, $org_id = null, $workos_id = null, $avatar = null): self
    {
        return new self(
            name: $name,
            email: $email,
            password: $password,
            org_id: $org_id,
            workos_id: $workos_id,
            avatar: $avatar,
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            org_id: $data['org_id'] ?? null,
            workos_id: $data['workos_id'] ?? null,
            avatar: $data['avatar'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'org_id' => $this->org_id,
            'workos_id' => $this->workos_id,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
