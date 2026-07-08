# laravel-hybrid-inertia

## Concept

A micro-package that lets a single Laravel controller action serve both an Inertia.js response (for browser requests) and a JSON response (for API/mobile clients) — with zero duplication.

The controller author marks an action with a `#[InertiaComponent('Page/Name')]` attribute. The package swaps the `ResponseFactory` binding so that every `response()->json(...)` call automatically checks for the `X-Inertia` header; if present, it renders the declared Inertia component instead.

## Use cases

1. **Migration aid** — progressively migrate a JSON API to Inertia without rewriting controllers all at once. Add the attribute, done. Remove it when the controller is fully rewritten.
2. **Permanent hybrid** — apps that serve both a web SPA (Inertia) and a mobile/external API from the same codebase. One controller, two presentations, same data/resource layer.

## API sketch

```php
// Controller
#[InertiaComponent('Reservations/Show')]
public function show(Reservation $reservation)
{
    return response()->json([
        'reservation' => ReservationResource::make($reservation),
    ]);
}
```

- Browser with `X-Inertia` header → `Inertia::render('Reservations/Show', $data)`
- Mobile / API client → plain JSON

## Package shape

- `InertiaComponent` — PHP 8 attribute, holds component name + optional extras (layout, guard)
- `HybridResponseFactory extends ResponseFactory` — overrides `json()`, reads attribute via reflection on current route action
- `HybridInertiaServiceProvider` — rebinds `ResponseFactory` contract, no config needed
- Optional artisan command: `hybrid:status` — lists all actions with/without the attribute (migration progress dashboard)

## Package name

`splitstack/laravel-hybrid-inertia`

## Notes

- Zero callsite changes required
- Works alongside existing Inertia setup
- Routes without the attribute are completely unaffected
- Attribute can carry extra metadata: `#[InertiaComponent('Foo', layout: 'AppLayout')]`
