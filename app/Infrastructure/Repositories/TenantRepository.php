<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Landlord\TenantEntity;
use App\Infrastructure\Contracts\TenantRepositoryInterface;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TenantRepository implements TenantRepositoryInterface
{
    /** @return Collection<TenantEntity> */
    public function all(): Collection
    {
        return Tenant::all()->map(
            fn (Tenant $model) => TenantEntity::fromArray($model->toArray()),
        );
    }

    public function findById(int|string $id): ?TenantEntity
    {
        $model = Tenant::find($id);

        return $model ? TenantEntity::fromArray($model->toArray()) : null;
    }

    public function save(TenantEntity $entity): TenantEntity
    {
        if (! $entity instanceof TenantEntity) {
            throw new \InvalidArgumentException('Expected TenantEntity');
        }

        $dbExists = DB::selectOne(
            'SELECT 1 FROM pg_database WHERE datname = ?',
            [$entity->database]
        ) !== null;

        if (! $dbExists) {
            $templateDB = config('splitstack.tenant_template_db', 'tenant_template');

            // CREATE DATABASE cannot run inside a transaction in Postgres
            DB::unprepared("CREATE DATABASE \"{$entity->database}\" WITH TEMPLATE {$templateDB}");
        }

        $model = Tenant::updateOrCreate(
            ['id' => $entity->id],
            $entity->toArray(),
        );

        logger()->debug('Tenant saved', ['tenant_id' => $model->id, 'database' => $model->database]);

        if ($model) {
            $model->makeCurrent();
        }

        return TenantEntity::fromArray($model->toArray());
    }

    public function delete(int|string $id): void
    {
        Tenant::destroy($id);
    }

    public function purge(TenantEntity $entity): void
    {
        Tenant::destroy($entity->id);

        $dbExists = DB::selectOne(
            'SELECT 1 FROM pg_database WHERE datname = ?',
            [$entity->database]
        ) !== null;

        if ($dbExists) {
            DB::unprepared("DROP DATABASE \"{$entity->database}\"");
        }
    }
}
