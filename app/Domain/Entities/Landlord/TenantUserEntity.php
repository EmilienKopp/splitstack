<?php

declare(strict_types=1);

namespace App\Domain\Entities\Landlord;

final readonly class TenantUserEntity
{
    public function __construct(
        public ?int $id = null,
        public ?int $org_id = null,
        public ?string $name = null,
        public ?string $domain = null,
        public ?string $space = null,
        public ?string $database = null,
        public ?string $hash = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromNameAndSlug(string $name, string $slug): self
    {
        return new self(
            name: $name,
            space: $slug,
            database: $slug.'_db',
            hash: hash('sha256', $slug),
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'] ?? null,
            org_id: $data['org_id'] ?? null,
            name: $data['name'] ?? null,
            domain: $data['domain'] ?? null,
            space: $data['space'] ?? null,
            database: $data['database'] ?? null,
            hash: $data['hash'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'org_id' => $this->org_id,
            'name' => $this->name,
            'domain' => $this->domain,
            'space' => $this->space,
            'database' => $this->database,
            'hash' => $this->hash,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
