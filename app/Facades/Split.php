<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for sending hybrid responses outside of a controller context.
 *
 * Proxies to the HybridController instance bound to the 'split' container key,
 * making respond() available as a static call from anywhere in the application.
 *
 * @method static \App\Http\Responses\SplitResponseBuilder respond(iterable $data = [], ?string $component = null, ?string $route = null)
 *
 * @see HybridController
 * @see HasHybridResponses
 */
class Split extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'split';
    }
}
