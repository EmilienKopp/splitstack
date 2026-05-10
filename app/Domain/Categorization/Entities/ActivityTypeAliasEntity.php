<?php

namespace App\Domain\Categorization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of ActivityType — no standalone repository.
 */
class ActivityTypeAliasEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $activity_type_id,
        public readonly ?int $organization_id,
        public readonly ?int $user_id,
        public readonly string $alias,
    ) {}
}
