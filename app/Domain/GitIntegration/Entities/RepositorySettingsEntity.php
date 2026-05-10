<?php

namespace App\Domain\GitIntegration\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Child entity of CodeRepository — no standalone repository.
 */
class RepositorySettingsEntity extends BaseEntity
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $repository_id,
        public readonly ?int $branch_id,
        public readonly array $excluded_folders,
        public readonly array $excluded_file_extensions,
    ) {}
}
