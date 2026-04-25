<?php

namespace App\Providers;

use App\Search\PgTextSearchEngine;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class SearchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        resolve(EngineManager::class)->extend('pg_textsearch', fn () => new PgTextSearchEngine);
    }
}
