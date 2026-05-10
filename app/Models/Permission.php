<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements PermissionContract
{
    use UsesTenantConnection;

    protected $fillable = [
        'name',
        'guard_name',
    ];
}
