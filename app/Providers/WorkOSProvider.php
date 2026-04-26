<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WorkOS\Organizations;
use WorkOS\UserManagement;

class WorkOSProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserManagement::class, function () {
            return new UserManagement;
        });
        $this->app->bind(Organizations::class, function () {
            return new Organizations;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
