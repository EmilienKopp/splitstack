<?php

namespace App\Http\Controllers\Auth;

use App\Application\Actions\Auth\CreateUser;
use App\Application\Actions\Auth\FindUser;
use App\Http\Controllers\Controller;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\URL;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;

class AuthController extends Controller
{
    public function login(AuthKitLoginRequest $request)
    {
        return $request->redirect();
    }

    public function authenticate(AuthKitAuthenticationRequest $request)
    {
        $request->authenticate(findUsing: app(FindUser::class), createUsing: app(CreateUser::class), updateUsing: null);

        $tenant = Tenant::current();
        $user = auth()->user();

        if (config('features.uses_teams')) {
            $currentTeam = $user->currentTeam ?? $user->personalTeam();

            if ($currentTeam && ! $user->current_team_id) {
                $user->switchTeam($currentTeam);
            }

            if ($currentTeam) {
                URL::defaults(['current_team' => $currentTeam->slug]);
            }
        }

        return redirect()->intended(route('dashboard', ['space' => $tenant?->space]));
    }

    public function logout(AuthKitLogoutRequest $request)
    {
        return $request->logout();
    }
}
