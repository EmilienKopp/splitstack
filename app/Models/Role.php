<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements RoleContract
{
    use UsesTenantConnection;

    protected $fillable = [
        'name',
        'guard_name',
    ];
}
