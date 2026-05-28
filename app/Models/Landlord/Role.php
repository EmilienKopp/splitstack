<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role as SpatieRole;

final class Role extends SpatieRole implements RoleContract
{
    use UsesLandlordConnection;

    protected $fillable = [
        'name',
        'guard_name',
    ];
}
