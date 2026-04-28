<?php

namespace App\Http\Middleware;

use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Uri;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $features = $this->getRelevantFeatures();
        $config = $this->getRelevantConfig();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentTeam' => fn () => $user?->currentTeam ? $user->toUserTeam($user->currentTeam) : null,
            'teams' => fn () => $user?->toUserTeams(includeCurrent: true) ?? [],
            'features' => $features,
            'config' => $config,
            'space' => Tenant::current()?->space,
        ];
    }

    public function getRelevantFeatures()
    {
        $user = request()->user();

        return $user?->features() ?? [];
    }

    public function getRelevantConfig()
    {
        $rootDomain = Uri::of(config('app.url'))->host();

        return [
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'rootDomain' => $rootDomain,
            ],
            'usesTeams' => config('features.uses_teams'),
        ];
    }
}
