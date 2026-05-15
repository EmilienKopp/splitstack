<?php

namespace App\Http\Middleware;

use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Uri;
use Inertia\Middleware;
use Spatie\Navigation\Navigation;

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
        $nav = Navigation::make()->tree();
        $nav = collect($nav)->map(fn ($item) => [
            ...$item,
            'href' => parse_url($item['url'], PHP_URL_PATH),
        ]);
        $crumbs = Navigation::make()->breadcrumbs();
        $crumbs = collect($crumbs)->map(fn ($item) => [
            ...$item,
            'href' => parse_url($item['url'], PHP_URL_PATH),
        ]);

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentTeam' => fn () => $user?->currentTeam ? $user->toUserTeam($user->currentTeam) : null,
            // 'teams' => fn () => $user?->toUserTeams(includeCurrent: true) ?? [], //TODO: figure out what to to with teams on UserEntity
            'features' => $features,
            'config' => $config,
            'space' => Tenant::current()?->space,
            'context' => [
                'tenant' => Context::get('tenantId'),
                'domain' => Context::get('domain'),
                'executionContext' => Context::get('executionContext'),
                'host' => Context::get('host'),
                'availableTenants' => Context::get('availableTenants'),
            ],
            'nav' => $nav,
            'crumbs' => $crumbs,
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
