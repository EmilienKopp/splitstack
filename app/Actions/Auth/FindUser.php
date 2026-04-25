<?php

namespace App\Actions\Auth;

use App\Exceptions\TenantNotFoundException;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\Context;

final class FindUser
{
    public function __invoke($user)
    {
        // Handle tenant finding and session storing here
        $orgId = $user->organizationId;

        $tenant = Tenant::where('org_id', $orgId)->first();

        if (! $tenant) {
            Context::add('user', $user);
            throw new TenantNotFoundException("Tenant with org_id {$orgId} not found.");
        }
    }
}
