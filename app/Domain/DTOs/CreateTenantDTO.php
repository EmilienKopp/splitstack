<?php

namespace App\Domain\DTOs;

use App\Application\Shared\Contracts\BaseDTO;

class CreateTenantDTO extends BaseDTO
{
    public function __construct(
        public readonly string $org_name,
        public readonly string $org_slug,
        public readonly ?string $org_id = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            org_name: $data['org_name'] ?? '',
            org_slug: $data['org_slug'] ?? '',
            org_id: $data['org_id'] ?? null,
        );
    }
}
