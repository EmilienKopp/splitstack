<?php

namespace App\Providers;

use App\Application\Shared\Contracts\TransactionHandler;
use App\Http\Controllers\HybridController;
use App\Http\Responses\SplitResponseBuilder;
use App\Infrastructure\DBTransactionHandler;
use Illuminate\Support\ServiceProvider;

class SplitstackProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            TransactionHandler::class,
            DBTransactionHandler::class
        );

        $this->app->bind(SplitResponseBuilder::class, function ($app) {
            return new SplitResponseBuilder;
        });

        $this->app->bind('split', function ($app) {
            return new HybridController($app->make(SplitResponseBuilder::class));
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
