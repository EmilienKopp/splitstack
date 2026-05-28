<?php

declare(strict_types=1);

namespace App\Providers;

use App\Search\PgTextSearchEngine;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

final class SearchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        resolve(EngineManager::class)->extend('pg_textsearch', fn (): PgTextSearchEngine => new PgTextSearchEngine);
    }
}
