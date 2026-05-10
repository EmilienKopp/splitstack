<?php

namespace App\Infrastructure;

use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;

class TenantFinder extends \Spatie\Multitenancy\TenantFinder\TenantFinder
{
    public function findForRequest(Request $_request): ?IsTenant
    {
        $space = get_subdomain();

        return Tenant::bySpace($space)->first();
    }
}
