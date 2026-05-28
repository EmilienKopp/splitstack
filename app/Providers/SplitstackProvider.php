<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Shared\Contracts\TransactionHandler;
use App\Http\Controllers\HybridController;
use App\Http\Responses\SplitResponseBuilder;
use App\Infrastructure\DBTransactionHandler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

final class SplitstackProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        foreach (File::allFiles(app_path('Infrastructure/Repositories')) as $file) {
            $class = 'App\\Infrastructure\\Repositories\\'.$file->getFilenameWithoutExtension();
            $interface = 'App\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension().'Interface';

            if (interface_exists($interface)) {
                $this->app->bind($interface, $class);
            }
        }

        $this->app->bind(
            TransactionHandler::class,
            DBTransactionHandler::class
        );

        $this->app->bind(SplitResponseBuilder::class, fn ($app): SplitResponseBuilder => new SplitResponseBuilder);

        $this->app->bind('split', fn ($app): HybridController => new HybridController($app->make(SplitResponseBuilder::class)));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
