<?php

namespace App\Domain\Categorization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Parent entity — load via TaskCategoryRepository.
 * Owns TaskCategoryAliasEntity.
 */
class TaskCategoryEntity extends BaseEntity
{
    /** @var TaskCategoryAliasEntity[] */
    private array $aliases = [];

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {}

    public function addAlias(TaskCategoryAliasEntity $alias): void
    {
        $this->aliases[] = $alias;
    }

    /** @return TaskCategoryAliasEntity[] */
    public function aliases(): array
    {
        return $this->aliases;
    }
}
