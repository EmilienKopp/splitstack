<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Application\UseCases\RegisterBothTenantAndUser;
use App\Domain\DTOs\CreateTenantDTO;
use App\Domain\DTOs\RegisterOnTheFlyDTO;
use App\Domain\DTOs\UserDTO;
use App\Facades\Split;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterOnTheFlyRequest;

final class RegistrationController extends Controller
{
    public function __construct(
        public readonly RegisterBothTenantAndUser $createTenantAndUser,
    ) {}

    public function showOnTheFlyForm()
    {
        $user = session()->get('pending_user');
        $org = session()->get('pending_org');
        $user['name'] = sprintf('%s %s', $user['firstName'], $user['lastName']);

        return inertia('RegisterOnTheFly', [
            'user' => $user,
            'org' => $org,
            'suggested_org_slug' => str($org['name'] ?? '')->slug(),
        ]);
    }

    public function registerOnTheFly(RegisterOnTheFlyRequest $request): \App\Http\Responses\SplitResponseBuilder
    {
        $data = RegisterOnTheFlyDTO::fromValidatable($request)->toArray();

        $tenantDTO = CreateTenantDTO::fromArray($data);
        $userDTO = UserDTO::fromArray($data);
        $this->createTenantAndUser->execute($tenantDTO, $userDTO);

        return Split::respond()
            ->route('dashboard', ['space' => $tenantDTO->org_slug]);
    }
}
