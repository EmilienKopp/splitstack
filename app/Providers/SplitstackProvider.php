<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class SplitstackProvider extends ServiceProvider
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
