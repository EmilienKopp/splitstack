<?php

namespace App\Domain\Entities\Landlord;

class TenantUserEntity
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $org_id = null,
        public readonly ?string $name = null,
        public readonly ?string $domain = null,
        public readonly ?string $host = null,
        public readonly ?string $database = null,
        public readonly ?string $hash = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {}

    public static function fromNameAndSlug(string $name, string $slug): self
    {
        return new self(
            name: $name,
            host: $slug,
            database: "{$slug}_db",
            hash: hash('sha256', $slug),
        );
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            org_id: $data['org_id'] ?? null,
            name: $data['name'] ?? null,
            domain: $data['domain'] ?? null,
            host: $data['host'] ?? null,
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
            'host' => $this->host,
            'database' => $this->database,
            'hash' => $this->hash,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
