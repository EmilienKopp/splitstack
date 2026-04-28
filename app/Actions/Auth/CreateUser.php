<?php

namespace App\Actions\Auth;

use App\Exceptions\TenantNotFoundException;
use App\Models\Landlord\Tenant;
use App\Models\User;
use WorkOS\Organizations;
use WorkOS\UserManagement;

final class CreateUser
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
            throw new TenantNotFoundException("Tenant with org_id {$user->organizationId} not found.");
        }

        $tenant->makeCurrent();
        $newUser = new User;
        $newUser->name = "{$user->firstName} {$user->lastName}";
        $newUser->email = $user->email;
        $newUser->workos_id = $user->id;
        $newUser->avatar = $user->avatar;
        $newUser->save();

        return $newUser;
    }
}
