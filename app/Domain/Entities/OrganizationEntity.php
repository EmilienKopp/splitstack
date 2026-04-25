<?php

namespace App\Domain\Entities;

class OrganizationEntity
{
    public function __construct(
        public readonly ?int $id = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
