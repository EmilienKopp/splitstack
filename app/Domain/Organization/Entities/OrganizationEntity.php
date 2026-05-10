<?php

namespace App\Domain\Organization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Parent entity — load via OrganizationRepository.
 */
class OrganizationEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $userId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $type,
        public readonly ?string $icon,
        public readonly ?array $metadata,
    ) {}
}
