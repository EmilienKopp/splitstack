<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

final class TenantUser extends Model
{
    use UsesLandlordConnection;

    protected $table = 'tenant_users';

    protected $guarded = ['id'];
}
