<?php

namespace App\Http\Controllers;

use App\Http\Concerns\HasHybridResponses;
use App\Http\Responses\SplitResponseBuilder;

/**
 * Base controller for hybrid JSON/Inertia responses.
 *
 * Extend this instead of Controller when a controller always needs to return
 * hybrid responses. Alternatively, use the HasHybridResponses trait directly
 * on any existing controller, or call the Split facade from outside a controller.
 *
 * @see HasHybridResponses
 * @see Split
 */
class HybridController extends Controller
{
    use HasHybridResponses;

    public function __construct(
        protected readonly SplitResponseBuilder $builder,
    ) {}

}
