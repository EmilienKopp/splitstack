<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\FindUser;
use App\Exceptions\TenantNotFoundException;
use App\Http\Controllers\Controller;
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
        // try {
        $request->authenticate(findUsing: app(FindUser::class));
        // } catch (TenantNotFoundException $e) {
        //     return redirect()->route('home')
        //       ->with(['showSupportContact' => true])
        //       ->withErrors(['error' => 'Tenant not found. Please contact support.']);
        // } catch (\Exception $e) {
        //     return redirect()->route('home')->withErrors(['error' => 'Authentication failed. Please try again.']);
        // }

        // Tenant finding and session storing
        // dd($request->all());

        $user = auth()->user();
        $currentTeam = $user->currentTeam ?? $user->personalTeam();

        if ($currentTeam && ! $user->current_team_id) {
            $user->switchTeam($currentTeam);
        }

        if ($currentTeam) {
            URL::defaults(['current_team' => $currentTeam->slug]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(AuthKitLogoutRequest $request)
    {
        return $request->logout();
    }
}
