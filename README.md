# Splitstack

**Svelte · PostgreSQL · Laravel · Inertia · TypeScript**

A Laravel + Svelte starter template with end-to-end type safety, built-in multi-tenancy, and a clean DDD architecture — so you start every project with the hard parts already done.

## Why Splitstack?

Most full-stack templates give you a running app. Splitstack gives you a **typed pipeline from database to UI** — annotate a PHP class, run one command, and your Svelte components have the interface. No manual syncing, no drift between backend shapes and frontend types.

```text
PHP models / DTOs  →  php artisan typegen  →  TypeScript interfaces  →  Svelte components
     ↑                                                                         ↑
 type-hinted,                                                          statically checked,
 validated at                                                          no runtime surprises
 the boundary
```

On top of that: real-time DB events with zero broker overhead, a multi-tenant architecture with teams, role-based UI config, and hybrid controllers that serve both JSON and Inertia from the same method.

---

## 🗃️ Stack

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

## ✨ What's Included

### 🔧 Backend

#### Hybrid Response System

Controllers can return JSON or an Inertia page from the same method — no branching, no duplicated logic.

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

**DTOs** extend `BaseDTO` and get `fromRequest()`, `fromArray()`, `fromEntity()`, and `toArray()` for free.

**Entities** have identity and represent domain objects that persist over time.

**Value Objects** are immutable and identity-less — they describe a concept by their value alone.

#### Multi-Tenancy (Landlord / Tenant)

Separate `Models/Landlord/` and `Models/` namespaces. `SplitstackProvider` auto-binds `Repository ↔ RepositoryInterface` pairs by naming convention — no manual registration needed.

#### 🔷 TypeScript Generation

Annotate a PHP class, run one command, get a TypeScript interface. Driven by config — define source directories, include/exclude filters, and output paths.

```bash
php artisan typegen
```

Your Svelte components stay in sync with your backend shapes automatically.

---

### 📦 In-Repo Packages (`packages/splitstack/`)

#### ⚡ Translucid — real-time DB events, no broker needed

Most real-time setups require Redis, a message broker, or a hosted service. Translucid uses **PostgreSQL's native `LISTEN/NOTIFY`** — the moment a row changes, your Svelte component knows about it. No Redis. No Pusher. No extra process.

**1. Add the trait to your model**

```php
use Splitstack\Translucid\Concerns\HasTranslucid;

class Order extends Model
{
    use HasTranslucid;
}
```

**2. Install the PG trigger once** (e.g. in a migration or seeder)

```php
Translucid::observe(Order::class);
// Installs a PostgreSQL trigger that fires pg_notify on every INSERT / UPDATE / DELETE
```

**3. Watch changes reactively in Svelte — single record**

```svelte
<script lang="ts">
    import { translucid } from 'translucid-svelte';
    import { onDestroy } from 'svelte';

    let { order } = $props();

    // Patches the local object in-place when the DB row changes
    const stop = translucid.table('orders').watch(order);
    onDestroy(stop);
</script>

<p>Status: {order.status}</p> <!-- updates live, no polling -->
```

**4. Watch changes reactively in Svelte — full collection**

```svelte
<script lang="ts">
    import { watchCollection } from 'translucid-svelte';
    import { onDestroy } from 'svelte';

    let { orders } = $props();

    const stop = watchCollection('orders', {
        onCreated(payload) { orders = [payload.data, ...orders]; },
    });
    onDestroy(stop);
</script>
```

Multi-tenant aware — each tenant's events are scoped to their own private channel, so there's no data leakage between tenants.

#### 🗄️ Stashable — attribute-driven repository caching

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

#### 🏷️ Metamon — typed metadata on Eloquent models

A `HandlesMetadata` trait that turns any JSON column into a first-class API:

```php
$model->meta('settings.theme', 'dark');     // set
$model->meta('settings.theme');             // get → 'dark'
$model->forgetMetadata('settings.theme');   // remove
User::whereMetadata('plan', 'pro')->get();  // query scope
```

Supports dot notation, role-scoped allowed keys, nesting depth limits, and size validation.

#### 🔢 EnumFriendly — Laravel-aware enum utilities

Add to any backed enum for instant Collection, validation rule, and select-option support:

```php
UserStatus::collect();           // Laravel Collection of values
UserStatus::toSelectOptions();   // [{value, label, name}, …]
UserStatus::rules(['required']); // ['required', 'string', 'in:active,inactive']
UserStatus::rule();              // Laravel Enum validation rule instance
```

---

### 🎨 Frontend

#### 👁️ Perspective — typed RBAC config system

Define role-variant configurations once; resolve them anywhere by role string. No conditionals, no scattered `if (role === 'admin')` checks scattered across your components.

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

#### 🔔 Flash Toast

Inertia's `flash` event is wired to `svelte-sonner` out of the box. Trigger a toast from any Laravel controller:

```php
session()->flash('toast', ['type' => 'success', 'message' => 'Saved!']);
```

#### 🛠️ Support Utilities (`lib/core/support/`)

Small, typed utility functions — no dependencies, no magic:

- **arrays** — `groupBy` (dot-notation key), `unique`, `mapUnique`, `mapColumn`
- **objects** — `dot(obj, 'a.b.c')` deep accessor
- **strings** — `capitalize`, `slugify`, `camelCase`, `snakeCase`, `kebabCase`
- **assessing** — `truthy`, `falsy`, `empty`, `exists` (handles arrays and objects correctly)
- **numbers**, **formatting**, **highlight**, **requests**

#### 🗺️ Wayfinder Integration

All controller routes are auto-generated as typed TypeScript functions in `resources/js/actions/`. No hardcoded URLs, no guessing route names.

```ts
import { DashboardController } from '@/actions/App/Http/Controllers';
router.visit(DashboardController.index.url({ space: slug }));
```

#### 🧩 UI Components

shadcn-svelte components under `components/ui/` — Avatar, Badge, Button, Card, Dialog, Dropdown, Select, Sidebar, Skeleton, Sonner, and more. Plus app-level components: `TeamSwitcher`, `NavMain`, `NavUser`, `AppSidebar`, `AppShell`.

---

## 🚀 Getting Started

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

## 🎯 Artisan Commands

```bash
php artisan split:make        # Scaffold Actions, UseCases, DTOs
php artisan split:migration   # Tenant-aware migration helper
php artisan tenant:tinker     # Tinker scoped to a tenant context
php artisan typegen           # Generate TypeScript interfaces from PHP classes
php artisan translucid:listen # Start the PG LISTEN loop
```

---

## 📁 Project Structure

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
