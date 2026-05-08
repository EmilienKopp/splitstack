<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class SplitResponseBuilder implements Responsable
{
    protected array $data = [];

    protected ?string $component = null;

    protected ?string $route = null;

    protected array $params = [];

    public function respond(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function component(?string $component): self
    {
        $this->component = $component;

        return $this;
    }

    public function route(?string $route, array $params = []): self
    {
        $this->route = $route;
        $this->params = $params;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        if ($this->route && $this->component) {
            throw new \InvalidArgumentException('Cannot specify both a component and a route for response.');
        }

        if (! $this->route && ! $this->component) {
            throw new \InvalidArgumentException('Must specify either a component or a route for response.');
        }

        if ($request->wantsJson()) {
            return response()->json($this->data);
        }

        if ($this->component) {
            return inertia($this->component, $this->data)->toResponse($request);
        }

        if ($this->route) {
            return inertia()->location(route($this->route, $this->params));
        }
    }
}
