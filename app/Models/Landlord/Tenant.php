<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Pennant\Concerns\HasFeatures;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

final class Tenant extends BaseTenant
{
    use HasFactory;
    use HasFeatures;
    use UsesLandlordConnection;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'space',
        'domain',
        'database',
        'org_id',
        'org_id',
    ];

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            // ...
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model): void {
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
}
