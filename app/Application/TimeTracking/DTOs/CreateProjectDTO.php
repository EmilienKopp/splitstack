<?php

namespace App\Application\TimeTracking\DTOs;

use App\Application\Shared\Contracts\BaseDTO;

class CreateProjectDTO extends BaseDTO
{
    public function __construct(
        public private(set) string $name,
        public private(set) string $status,
        public private(set) string $type,
        public private(set) int $user_id,
        public private(set) ?string $description = null,
        public private(set) ?int $organization_id = null,
        public private(set) ?string $start_date = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: $data['name'],
            status: $data['status'],
            type: $data['type'],
            user_id: $data['user_id'],
            description: $data['description'] ?? null,
            organization_id: $data['organization_id'] ?? null,
            start_date: $data['start_date'] ?? null,
        );
    }
}
