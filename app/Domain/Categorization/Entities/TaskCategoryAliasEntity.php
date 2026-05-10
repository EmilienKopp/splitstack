<?php

namespace App\Domain\Categorization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of TaskCategory — no standalone repository.
 */
class TaskCategoryAliasEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $task_category_id,
        public readonly ?int $organization_id,
        public readonly ?int $user_id,
        public readonly string $alias,
    ) {}
}
