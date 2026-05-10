<?php

namespace App\Providers;

use App\Domain\TimeTracking\Contracts\ClockEntryRepository;
use App\Domain\TimeTracking\Contracts\DailyLogRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class EntityBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Route::bind('clock_entry', function (string $value) {
            return app(ClockEntryRepository::class)->find(id: $value);
        });
        Route::bind('daily_log', function (string $value) {
            return app(DailyLogRepository::class)->find(id: $value);
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
