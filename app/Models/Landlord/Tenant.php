<?php

namespace App\Models\Landlord;

use App\Domain\Landlord\Entities\TenantEntity;
use App\Models\Concerns\HasEntity;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Pennant\Concerns\HasFeatures;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasEntity, HasFactory, HasFeatures, UsesLandlordConnection;

    protected $fillable = [
        'id',
        'name',
        'space',
        'domain',
        'database',
        'org_id',
        'org_id',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            // ...
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    #[Scope]
    protected function bySpace($query, $space): void
    {
        $query->where('space', $space);
    }

    public function toEntity(): TenantEntity
    {
        return new TenantEntity(
            id: $this->id,
            org_id: $this->org_id,
            name: $this->name,
            domain: $this->domain,
            space: $this->space,
            database: $this->database,
            hash: $this->hash,
            created_at: $this->created_at?->toDateTimeString(),
            updated_at: $this->updated_at?->toDateTimeString(),
        );
    }
}
