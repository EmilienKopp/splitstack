<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WorkOS\Organizations;
use WorkOS\UserManagement;

final class WorkOSProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserManagement::class, fn (): UserManagement => new UserManagement);
        $this->app->bind(Organizations::class, fn (): Organizations => new Organizations);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
