<?php

use App\Providers\AppServiceProvider;
use App\Providers\SearchServiceProvider;
use App\Providers\SplitstackProvider;
use App\Providers\WorkOSProvider;
use Splitstack\Translucid\Providers\TranslucidServiceProvider;

return [
    AppServiceProvider::class,
    SearchServiceProvider::class,
    SplitstackProvider::class,
    WorkOSProvider::class,
    TranslucidServiceProvider::class,
];
