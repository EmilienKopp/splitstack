<?php

namespace App\Domain\Categorization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Parent entity — load via ActivityTypeRepository.
 * Owns ActivityTypeAliasEntity.
 */
class ActivityTypeEntity extends BaseEntity
{
    /** @var ActivityTypeAliasEntity[] */
    private array $aliases = [];

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $color,
        public readonly ?string $icon,
    ) {}

    public function addAlias(ActivityTypeAliasEntity $alias): void
    {
        $this->aliases[] = $alias;
    }

    /** @return ActivityTypeAliasEntity[] */
    public function aliases(): array
    {
        return $this->aliases;
    }
}
