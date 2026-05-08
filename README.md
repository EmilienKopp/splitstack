# Splitstack

**Svelte · PostgreSQL · Laravel · Inertia · TypeScript**

A production-ready Laravel + Svelte starter template with end-to-end type safety, built-in multi-tenancy, and a clean DDD architecture — so you start every project with the hard parts already done.

### The pitch

Most full-stack templates give you a running app. Splitstack gives you a typed pipeline from database to UI — annotate a PHP class, run one command, and your Svelte components have the interface. No manual syncing, no drift between backend shapes and frontend types.

```
PHP models / DTOs  →  php artisan typegen  →  TypeScript interfaces  →  Svelte components
     ↑                                                                         ↑
 type-hinted,                                                          statically checked,
 validated at                                                          no runtime surprises
 the boundary
```

On top of that: a multi-tenant architecture with teams, role-based UI config, real-time DB events without a broker, and hybrid controllers that serve both JSON and Inertia from the same method.

---

## Stack

| Layer     | Technology                                     |
| --------- | ---------------------------------------------- |
| Backend   | Laravel 13, PHP 8.4                            |
| Frontend  | Svelte 5, TypeScript, Inertia.js v3            |
| Database  | PostgreSQL                                     |
| Styling   | Tailwind CSS v4 + shadcn-svelte                |
| Real-time | Laravel Reverb + Translucid (PG LISTEN/NOTIFY) |
| Auth      | WorkOS AuthKit                                 |
| Search    | Laravel Scout (PG full-text engine included)   |
| Build     | Vite, Wayfinder, Pint, Pest 4                  |

---

## What's Included

### Backend

#### Hybrid Response System

Controllers can return either JSON or an Inertia page from the same method — no branching, no duplicated logic.

```php
// Works for API clients (returns JSON) and browser requests (returns Inertia page)
return Split::respond(['user' => $user])->component('Dashboard');

// Or redirect to a named route
return Split::respond()->route('dashboard', ['space' => $slug]);
```

Use `HybridController` as your base, mix in `HasHybridResponses`, or call `Split::` from anywhere.

#### DDD Application Layer

A clean separation between HTTP, domain logic, and infrastructure:

```text
app/
├── Application/
│   ├── Actions/      # Single-responsibility handlers (CreateTenant, CreateUser…)
│   ├── Queries/      # Read-side handlers (CQRS)
│   ├── UseCases/     # Orchestrate multiple Actions with rollback on failure
│   └── Shared/
│       └── Contracts/ # BaseDTO, DTO, TransactionHandler
├── Domain/
│   ├── DTOs/         # Input carriers: move validated request data across layer boundaries
│   ├── Entities/     # Domain objects with identity (Tenant, User…)
│   └── ValueObjects/ # Immutable, identity-less domain concepts
└── Infrastructure/
    ├── Repositories/ # Data access behind interfaces, auto-bound by convention
    └── DBTransactionHandler.php
```

**DTOs** extend `BaseDTO` and get `fromRequest()`, `fromArray()`, `fromEntity()`, and `toArray()` for free. They carry input across boundaries.

**Entities** have identity and represent domain objects that persist over time.

**Value Objects** are immutable and identity-less — they describe a concept by their value alone.

#### Multi-Tenancy (Landlord / Tenant)

Separate `Models/Landlord/` and `Models/` namespaces. `SplitstackProvider` auto-binds `Repository ↔ RepositoryInterface` pairs by naming convention — no manual registration.

#### TypeScript Generation

Annotate PHP classes with `TypeScriptConvertible`, run `php artisan typegen`, and get typed interfaces in your frontend automatically. Driven by config — define source directories, include/exclude filters, and output paths.

---

### In-Repo Packages (`packages/splitstack/`)

#### Translucid — PostgreSQL real-time events

Use PG `LISTEN/NOTIFY` as a zero-broker event bus. One call installs a trigger on any table:

```php
Translucid::observe(Order::class);
// Now fires TranslucidCreated/Updated/Deleted events on every DB change
```

Multi-tenant aware — scopes channels per tenant so each tenant only receives their own events. No Redis, no Pusher, no extra process.

#### Stashable — attribute-driven repository caching

Annotate a repository method and get tenant-aware caching with a single decorator:

```php
#[WithCache(ttl: 300, key: 'orders.{id}')]
public function findById(int $id): Order { … }

// Then call:
OrderRepository::cache('findById', $id);   // cache or compute
OrderRepository::refresh('findById', $id); // bust and recompute
OrderRepository::fresh('findById', $id);   // bypass cache entirely
```

Cache keys are automatically prefixed by tenant. Supports tag-based bulk flush per tenant.

#### Metamon — typed metadata on Eloquent models

A `HandlesMetadata` trait that turns any JSON column into a first-class API:

```php
$model->meta('settings.theme', 'dark');     // set
$model->meta('settings.theme');             // get → 'dark'
$model->forgetMetadata('settings.theme');   // remove
User::whereMetadata('plan', 'pro')->get();  // query scope
```

Supports dot notation, role-scoped allowed keys, nesting depth limits, and size validation.

#### EnumFriendly — Laravel-aware enum utilities

Add to any backed enum for instant Collection, validation rule, and select-option support:

```php
UserStatus::collect();           // Laravel Collection of values
UserStatus::toSelectOptions();   // [{value, label, name}, …]
UserStatus::rules(['required']); // ['required', 'string', 'in:active,inactive']
UserStatus::rule();              // Laravel Enum validation rule instance
```

---

### Frontend

#### Perspective — typed RBAC config system

Define role-variant configurations once; resolve them anywhere by role string. No conditionals, no scattered `if (role === 'admin')` checks.

```ts
// perspectives/users.ts
export default new Perspective<UserTableConfig>(
    {
        admin:  () => ({ headers: [...adminHeaders], actions: ['Edit', 'Delete'] }),
        viewer: () => ({ headers: [...viewerHeaders], actions: [] }),
    },
    () => ({ headers: [...defaultHeaders], actions: [] }) // fallback
);

// In a component:
const config = usersPerspective.for(currentUser.role);
```

Works for navigation, table columns, available actions, visible UI sections — anything that varies by role. The included `navigationPerspective` is wired to the sidebar out of the box.

#### Flash Toast

Inertia's `flash` event is wired to `svelte-sonner` out of the box. Flash a toast from any Laravel controller:

```php
session()->flash('toast', ['type' => 'success', 'message' => 'Saved!']);
```

#### Support Utilities (`lib/core/support/`)

Small, typed utility functions — no dependencies, no magic:

- **arrays** — `groupBy` (dot-notation key), `unique`, `mapUnique`, `mapColumn`
- **objects** — `dot(obj, 'a.b.c')` deep accessor
- **strings** — `capitalize`, `slugify`, `camelCase`, `snakeCase`, `kebabCase`
- **assessing** — `truthy`, `falsy`, `empty`, `exists` (handles arrays and objects correctly)
- **numbers**, **formatting**, **highlight**, **requests**

#### Wayfinder Integration

All controller routes are auto-generated as typed TypeScript functions in `resources/js/actions/`. No hardcoded URLs, no guessing route names.

```ts
import { DashboardController } from '@/actions/App/Http/Controllers';
router.visit(DashboardController.index.url({ space: slug }));
```

#### UI Components

shadcn-svelte components under `components/ui/` — Avatar, Badge, Button, Card, Dialog, Dropdown, Select, Sidebar, Skeleton, Sonner, and more. Plus app-level components: `TeamSwitcher`, `NavMain`, `NavUser`, `AppSidebar`, `AppShell`.

---

## Getting Started

```bash
git clone https://github.com/EmilienKopp/splitstack
cd splitstack
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```

> PostgreSQL is required. Docker Compose config is included for a local DB + pgAdmin setup.

---

## Artisan Commands

```bash
php artisan split:make        # Scaffold Actions, UseCases, DTOs
php artisan split:migration   # Tenant-aware migration helper
php artisan tenant:tinker     # Tinker scoped to a tenant context
php artisan typegen           # Generate TypeScript interfaces from PHP classes
php artisan translucid:listen # Start the PG LISTEN loop
```

---

## Project Structure

```text
app/
├── Application/
│   ├── Actions/       # Single-responsibility handlers
│   ├── Queries/       # Read-side handlers
│   ├── UseCases/      # Multi-step orchestration
│   └── Shared/
│       └── Contracts/ # BaseDTO, DTO, TransactionHandler
├── Console/Commands/  # Custom Artisan commands
├── Domain/
│   ├── DTOs/          # Input carriers (extend BaseDTO)
│   ├── Entities/      # Domain objects with identity
│   └── ValueObjects/  # Immutable, identity-less concepts
├── Facades/           # Split facade
├── Http/
│   ├── Concerns/      # HasHybridResponses trait
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Responses/     # SplitResponseBuilder
├── Infrastructure/    # Repositories, DB transaction handler
├── Models/
│   └── Landlord/      # Cross-tenant models (Tenant, User, Role…)
├── Providers/         # SplitstackProvider (auto-binding)
└── Support/TypeScript/ # Codegen pipeline

packages/splitstack/
├── translucid/        # PG LISTEN/NOTIFY real-time eventing
├── laravel-stashable/ # Attribute-driven repository caching
├── laravel-metamon/   # JSON metadata trait for Eloquent
└── laravel-enum-friendly/ # Enum utilities for Laravel

resources/js/
├── components/ui/     # shadcn-svelte primitives
├── lib/core/          # Perspective class + support utilities
├── perspectives/      # RBAC config definitions
├── actions/           # Wayfinder-generated route functions
└── types/             # Shared TypeScript types
```

---

## License

MIT
