<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Currency extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_first',
        'is_default',
        'exchange_rate',
    ];

    protected $casts = [
        'symbol_first' => 'boolean',
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:8',
    ];
}
