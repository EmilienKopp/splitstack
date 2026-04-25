<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Pennant\Concerns\HasFeatures;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasFactory, HasFeatures, UsesLandlordConnection;

    protected $fillable = [
        'id',
        'name',
        'host',
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
}
