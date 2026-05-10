<?php

namespace App\Domain\Identity\Entities;

use App\Domain\Shared\BaseEntity;
use App\Domain\Shared\ValueObjects\ID;

/**
 * Parent entity — load via UserRepository.
 * WorkOS is the source of truth for auth — this entity holds local profile data only.
 */
class UserEntity extends BaseEntity
{
    public function __construct(
        public ?ID $id = null,
        public ?string $workos_id = null,
        public ?string $handle = null,
        public ?string $name = null,
        public ?string $first_name = null,
        public ?string $last_name = null,
        public ?string $email = null,
        public ?string $avatar = null,
        public string $timezone = 'UTC',
        public ?array $preferences = null,
        public ?array $cli_config = null,
        public ?ID $current_team_id = null,
        public ?ID $org_id = null,
        public ?string $password = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
        if (! $this->handle && $this->name) {
            $this->handle = strtolower(str_replace(' ', '_', $this->name)).'_'.substr((string) $this->workos_id, 0, 8);
        } elseif (! $this->handle) {
            $this->handle = $this->workos_id;
        }
    }

    public static function fromAuthInfo($name, $email, $password, ?ID $org_id = null, ?ID $workos_id = null, ?string $avatar = null): self
    {
        return new self(
            name: $name,
            first_name: explode(' ', $name)[0] ?? null,
            last_name: explode(' ', $name)[1] ?? null,
            email: $email,
            password: $password,
            org_id: $org_id,
            workos_id: $workos_id,
            avatar: $avatar,
        );
    }
}
