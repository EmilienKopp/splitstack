<?php

namespace App\Http\Concerns;

use App\Http\Responses\SplitResponseBuilder;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\URL;

/**
 * Provides a respond() method that returns the appropriate response type
 * depending on the request and the target destination.
 *
 * Can be mixed into any controller, or used via the Split facade.
 *
 * @see HybridController
 * @see Split
 */
trait HasHybridResponses
{
    /**
     * Start a fluent split response builder.
     */
    public function respond(iterable $data = [], ?string $component = null, ?string $route = null, $params = []): SplitResponseBuilder
    {
        URL::defaults(['space' => Tenant::current()?->space]);
        $builder = $this->builder instanceof SplitResponseBuilder
            ? $this->builder
            : app()->make(SplitResponseBuilder::class);
        $data = is_array($data) ? $data : iterator_to_array($data);

        return $builder->respond($data)->component($component)->route($route, $params);
    }
}
