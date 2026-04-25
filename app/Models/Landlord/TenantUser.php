<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class TenantUser extends Model
{
    use UsesLandlordConnection;

    protected $table = 'tenant_users';

    protected $guarded = ['id'];
}
