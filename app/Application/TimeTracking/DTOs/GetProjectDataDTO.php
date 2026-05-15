<?php

namespace App\Application\TimeTracking\DTOs;

final class GetProjectDataDTO
{
    public function __construct(
        public readonly int|string $id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }
}
