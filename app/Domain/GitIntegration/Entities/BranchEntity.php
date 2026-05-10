<?php

namespace App\Domain\GitIntegration\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of CodeRepository — no standalone repository.
 */
class BranchEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $repository_id,
        public readonly string $name,
        public readonly bool $is_default,
    ) {}
}
