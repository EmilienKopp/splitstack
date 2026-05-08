<?php

namespace App\Http\Controllers\Auth;

use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\DTOs\RegisterOnTheFlyDTO;
use App\Domain\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterOnTheFlyRequest;
use App\UseCases\RegisterBothTenantAndUser;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function __construct(
        public readonly RegisterBothTenantAndUser $createTenantAndUser,
    ) {}

    public function showOnTheFlyForm()
    {
        $user = session()->get('pending_user');
        $org = session()->get('pending_org');
        $user['name'] = "{$user['firstName']} {$user['lastName']}";

        return inertia('RegisterOnTheFly', [
            'user' => $user,
            'org' => $org,
            'suggested_org_slug' => str($org['name'] ?? '')->slug(),
        ]);
    }

    public function registerOnTheFly(RegisterOnTheFlyRequest $request)
    {
        $data = RegisterOnTheFlyDTO::fromRequest($request)->toArray();

        $tenantDTO = CreateTenantDTO::fromArray($data);
        $userDTO = UserDTO::fromArray($data);
        $this->createTenantAndUser->execute($tenantDTO, $userDTO);

        $dashboardUrl = route('dashboard', ['space' => $tenantDTO->org_slug]);

        if ($request->inertia()) {
            return Inertia::location($dashboardUrl);
        }

        return redirect()->to($dashboardUrl);
    }
}
