<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\OrganizationEntity;
use App\Infrastructure\Contracts\OrganizationRepositoryInterface;
use App\Models\Organization;
use Illuminate\Support\Collection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    /** @return Collection<OrganizationEntity> */
    public function all(): Collection
    {
        return Organization::all()->map(
            fn (Organization $model) => OrganizationEntity::fromArray($model->toArray()),
        );
    }

    public function findById(int $id): ?OrganizationEntity
    {
        $model = Organization::find($id);

        return $model ? OrganizationEntity::fromArray($model->toArray()) : null;
    }

    public function save(OrganizationEntity $entity): OrganizationEntity
    {
        if (! $entity instanceof OrganizationEntity) {
            throw new \InvalidArgumentException('Expected OrganizationEntity');
        }

        $model = Organization::updateOrCreate(
            ['id' => $entity->id],
            $entity->toArray(),
        );

        return OrganizationEntity::fromArray($model->toArray());
    }

    public function delete(int $id): void
    {
        Organization::destroy($id);
    }
}
