<?php

namespace App\Domain\GitIntegration\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Parent entity — load via CodeRepositoryRepository.
 * Owns BranchEntity and RepositorySettingsEntity.
 */
class CodeRepositoryEntity extends BaseEntity
{
    /** @var BranchEntity[] */
    private array $branches = [];

    private ?RepositorySettingsEntity $settings = null;

    public function __construct(
        public readonly ?int $id,
        public readonly int $user_id,
        public readonly ?int $github_connection_id,
        public readonly ?int $project_id,
        public readonly string $name,
        public readonly string $url,
        public readonly ?string $path,
    ) {}

    public function addBranch(BranchEntity $branch): void
    {
        $this->branches[] = $branch;
    }

    public function setSettings(RepositorySettingsEntity $settings): void
    {
        $this->settings = $settings;
    }

    public function defaultBranch(): ?BranchEntity
    {
        foreach ($this->branches as $branch) {
            if ($branch->is_default) {
                return $branch;
            }
        }

        return null;
    }

    /** @return BranchEntity[] */
    public function branches(): array
    {
        return $this->branches;
    }

    public function settings(): ?RepositorySettingsEntity
    {
        return $this->settings;
    }
}
