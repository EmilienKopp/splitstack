<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Exceptions\TenantNotFoundException;
use App\Models\Landlord\Tenant;
use App\Models\User;
use WorkOS\Organizations;
use WorkOS\UserManagement;

final class FindUser
{
    public function __construct(public UserManagement $userManagement, public Organizations $organizations) {}

    public function __invoke($user)
    {
        $tenant = Tenant::where('org_id', $user->organizationId)->first();
        $org = $this->organizations->getOrganization($user->organizationId);

        if (! $tenant) {
            session()->put([
                'pending_user' => $user,
                'pending_org' => $org->toArray(),
            ]);
            throw new TenantNotFoundException(sprintf('Tenant with org_id %s not found.', $user->organizationId));
        }

        $tenant->makeCurrent();

        return User::where('email', $user->email)->first();
    }
}
