<?php

namespace App\Infrastructure;

use App\Infrastructure\Contracts\TenantRepositoryInterface;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;

class TenantFinder extends \Spatie\Multitenancy\TenantFinder\TenantFinder
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenantRepository,
    ) {}

    public function findForRequest(Request $request): ?IsTenant
    {
        $host = $request->getHost();

        $tenant = Tenant::where('domain', $host)->first();

        if (! $tenant) {
            return null;
        }

        return $tenant;
    }
}
