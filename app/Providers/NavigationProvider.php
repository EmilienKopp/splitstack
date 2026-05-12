<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Navigation\Navigation;

class NavigationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->resolving(Navigation::class, function (Navigation $navigation): Navigation {
            return $navigation
                ->add(__('Dashboard'), route('dashboard'))
                ->add(__('Projects'), route('projects.index'))
                ->add(__('Organizations'), route('organization.index'));
        });
    }
}
