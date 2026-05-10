# Qadran — Architecture Guidelines

This document defines the architectural conventions for the Qadran project. All agents and LLMs
contributing to this codebase must follow these rules.

---

## Core Principle

This project uses a **clean(ish) layered architecture**:

```
HTTP Layer → Use Case Layer → Domain Layer → Repository Layer → Persistence
     ↕ (via Inertia)
Frontend (Svelte 5 / Inertia)
```

Each layer has a single responsibility. Do not bleed logic across layers.

---

## Layer Rules

### 1. Controllers (`app/Http/Controllers/`)

- **Thin only.** Controllers orchestrate; they do not contain business logic.
- Resolve input from the request, call a use case, return an Inertia response or redirect.
- Use **constructor injection** for use cases and repositories.
- Always validate with a dedicated `FormRequest` class — never inline validation.
- Return `Inertia::render()` for page renders, `redirect()->route()` for post-action redirects.
- Controllers must not know about domain entities directly — they pass validated scalars or DTOs
  to use cases and receive whatever the use case returns.

```php
// CORRECT
class StopTimerController extends Controller
{
    public function __construct(private readonly StopTimer $stopTimer) {}

    public function __invoke(StopTimerRequest $request): RedirectResponse
    {
        $this->stopTimer->execute(
            new StopTimerCommand(
                userId: Auth::id(),
                dailyLogId: $request->daily_log_id,
                stoppedAt: now(),
            )
        );

        return redirect()->route('dashboard');
    }
}

// WRONG — business logic in controller
public function __invoke(Request $request): RedirectResponse
{
    $log = DailyLog::find($request->daily_log_id);
    $log->total_seconds += $request->duration;
    $log->save();
    return redirect()->route('dashboard');
}
```

---

### 2. Use Cases (`app/Application/UseCases/`)

- **One use case per business operation.**
- Named with an imperative verb phrase: `StopTimer`, `CreateDailyLog`, `ApplyRate`.
- Single public method: `execute()` with a typed Command object and explicit return type.
- Accept **Command objects** (plain DTOs) as input — never raw arrays or HTTP requests.
- Return **domain entities or scalar values** — never Eloquent models.
- Use constructor-injected **repository interfaces** for persistence — never touch Eloquent directly.
- Use cases may dispatch Laravel events directly — the framework event system is treated as a
  native language feature, not an infrastructure concern to abstract away.
- Use cases must not know about HTTP, sessions, or `Auth::` — those are resolved by the controller
  before calling the use case.

```php
// CORRECT
class StopTimer
{
    public function __construct(
        private readonly DailyLogRepository $dailyLogRepo,
    ) {}

    public function execute(StopTimerCommand $cmd): DailyLogEntity
    {
        $log = $this->dailyLogRepo->findById($cmd->dailyLogId);
        $log->stopTimer($cmd->stoppedAt);
        $this->dailyLogRepo->save($log);

        ClockEntryCreated::dispatch($log->id, $cmd->stoppedAt);

        return $log;
    }
}

// WRONG — use case queries Eloquent directly
public function execute(StopTimerCommand $cmd): void
{
    DailyLogModel::find($cmd->dailyLogId)->update(['total_seconds' => ...]);
}
```

---

### 3. Commands (`app/Application/Commands/`)

- Plain readonly DTOs carrying input data into a use case.
- No logic — just typed properties.
- Named after the use case they serve: `StopTimerCommand`, `CreateDailyLogCommand`.

```php
readonly class StopTimerCommand
{
    public function __construct(
        public int $userId,
        public int $dailyLogId,
        public DateTimeInterface $stoppedAt,
    ) {}
}
```

---

### 4. Domain Entities (`app/Domain/{Domain}/Entities/`)

- Named `NounEntity` — e.g., `DailyLogEntity`, `ProjectEntity`, `ClockEntryEntity`.
- Contain **business behavior** — not just data bags.
- Enforce their own invariants: if a rule can be broken, the entity prevents it.
- **Parent entities** have an associated Repository and are the unit of persistence.
- **Child entities** have no Repository and are always loaded through their parent. Document this
  with a docblock: `Child entity of DailyLogEntity — no standalone repository.`
- Do not import Eloquent models, HTTP concerns, or repositories.
- Do not use Laravel facades or static calls — plain PHP only.

```php
// Parent entity — has DailyLogRepository
class DailyLogEntity
{
    private array $clockEntries = [];

    public function stopTimer(DateTimeInterface $at): void
    {
        if (!$this->isRunning()) {
            throw new TimerNotRunning();
        }

        $entry = new ClockEntryEntity(
            dailyLogId: $this->id,
            in: $this->startedAt,
            out: $at,
        );

        $this->clockEntries[] = $entry;
        $this->recalculateTotal(); // enforces the invariant
    }

    private function recalculateTotal(): void
    {
        $this->totalSeconds = array_sum(
            array_map(fn($e) => $e->durationSeconds, $this->clockEntries)
        );
    }
}

// Child entity — loaded through DailyLogEntity, no repository
/**
 * Child entity of DailyLogEntity — no standalone repository.
 * Load via DailyLogRepository::findById().
 */
class ClockEntryEntity
{
    public function __construct(
        public readonly int $dailyLogId,
        public readonly DateTimeInterface $in,
        public readonly DateTimeInterface $out,
    ) {}
}
```

---

### 5. Value Objects (`app/Domain/{Domain}/ValueObjects/`)

- Immutable — no setters, `readonly` properties.
- Compared by value, not identity — no ID.
- Validate constraints in the constructor; throw on invalid input.
- Transformations return a new instance, never mutate in place.
- Named as plain nouns: `Duration`, `Money`, `Timezone`, `DateRange`.
- Shared value objects (`Money`, `DateRange`, `Timezone`) live in `app/Domain/Shared/ValueObjects/`
  and may be imported across domains — this is the only permitted cross-domain import.

```php
readonly class Money
{
    public function __construct(
        public float $amount,
        public string $currency,
    ) {
        if ($this->amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatch();
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
```

---

### 6. Domain Events (`app/Domain/{Domain}/Events/`)

- Named in past tense: `ClockEntryCreated`, `TimerStopped`, `BudgetAdjusted`.
- Use the `Dispatchable` trait only — do not use `SerializesModels` or `InteractsWithSockets`
  unless the event itself is a broadcast event.
- Carry only the data listeners need — IDs and value objects, never full entities or Eloquent models.
- Dispatched explicitly from use cases via `EventName::dispatch(...)` after persistence.
- Laravel's event system is treated as a language-level feature — no wrapping interface needed.

```php
class ClockEntryCreated
{
    use Dispatchable;

    public function __construct(
        public readonly int $dailyLogId,
        public readonly int $userId,
        public readonly Duration $duration,
        public readonly DateTimeInterface $occurredAt,
    ) {}
}
```

---

### 7. Repositories (`app/Infrastructure/Persistence/`)

**Interfaces** live in `app/Domain/{Domain}/Contracts/`.
**Implementations** live in `app/Infrastructure/Persistence/Eloquent/`.

- All repository interfaces are defined in the Domain layer — dependency direction flows inward.
- Implementations convert between Eloquent models and domain entities:
  - Write: `Entity → toArray() → Model::updateOrCreate()`
  - Read: `Model::find() → hydrate Entity from attributes`
- Parent entities are always loaded with their child entities — the repository assembles the full
  aggregate in one operation.
- Repositories are the **only place** Eloquent models are touched.
- Register bindings in `app/Providers/RepositoryServiceProvider.php`.
- Always inject the interface, never the concrete implementation.

```php
// Interface — lives in Domain layer
interface DailyLogRepository
{
    public function findById(int $id): DailyLogEntity;
    public function findByUserAndDate(int $userId, DateTimeInterface $date): ?DailyLogEntity;
    public function save(DailyLogEntity $log): void;
}

// Implementation — lives in Infrastructure
class EloquentDailyLogRepository implements DailyLogRepository
{
    public function findById(int $id): DailyLogEntity
    {
        $model = DailyLogModel::with(['clockEntries'])->findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(DailyLogEntity $log): void
    {
        DailyLogModel::updateOrCreate(
            ['id' => $log->id],
            $log->toArray(),
        );
        // child entities saved here too
    }

    private function hydrate(DailyLogModel $model): DailyLogEntity
    {
        // construct DailyLogEntity with its ClockEntryEntity children
    }
}
```

---

### 8. Eloquent Models (`app/Models/`)

- Models are a **persistence detail only** — they are not passed across layers.
- Keep models lean: relationships, casts, fillable. No business logic.
- The domain and application layers must never import a Model class.
- Model names use the `Model` suffix to make the distinction explicit:
  `DailyLogModel`, `ProjectModel`, `ClockEntryModel`.

---

## Frontend: Prefer Inertia Over fetch

**Default rule: use Inertia, not `fetch`/`axios`.**

### Inertia Patterns (Svelte 5)

| Need                             | Pattern                                            |
| -------------------------------- | -------------------------------------------------- |
| Navigate to a page               | `router.visit(url)`                                |
| Submit a form                    | `router.post(url, data)`                           |
| Partial refresh (preserve state) | `router.post(url, data, { preserveState: true })`  |
| Full refresh after mutation      | `router.post(url, data, { preserveState: false })` |
| Reactive props                   | `let { myProp } = $props()`                        |

### When fetch Is Acceptable

Only use `fetch` when:

- The endpoint returns **non-page data** (e.g., a JSON lookup, a CLI sync payload).
- The operation must be **silent** (no page transition).
- You are calling a **third-party API** directly from the client.
- Real-time data arrives via **Reverb/WebSocket** — use the Echo client, not polling.

---

## Naming Conventions

| Layer                      | Convention                                   | Examples                                              |
| -------------------------- | -------------------------------------------- | ----------------------------------------------------- |
| Use Cases                  | `VerbNoun`                                   | `StopTimer`, `CreateDailyLog`, `ApplyRate`            |
| Commands                   | `VerbNounCommand`                            | `StopTimerCommand`, `CreateDailyLogCommand`           |
| Entities                   | `NounEntity`                                 | `DailyLogEntity`, `ProjectEntity`, `ClockEntryEntity` |
| Value Objects              | `Noun`                                       | `Duration`, `Money`, `Timezone`                       |
| Domain Events              | `NounVerbed` (past tense)                    | `ClockEntryCreated`, `TimerStopped`                   |
| Repository Interfaces      | `NounRepository`                             | `DailyLogRepository`, `ProjectRepository`             |
| Repository Implementations | `Eloquent{Noun}Repository`                   | `EloquentDailyLogRepository`                          |
| Eloquent Models            | `NounModel`                                  | `DailyLogModel`, `ClockEntryModel`                    |
| Controllers                | `VerbNounController`                         | `StopTimerController`, `StartTimerController`         |
| Form Requests              | `VerbNounRequest`                            | `StopTimerRequest`, `CreateProjectRequest`            |
| Svelte Pages               | `kebab-case.svelte` in `resources/js/pages/` | `dashboard.svelte`, `daily-log.svelte`                |
| Svelte Components          | `PascalCase.svelte`                          | `ClockEntry.svelte`, `ProjectSelector.svelte`         |

### Entity Property Casing

Entity properties use `snake_case` to match database column names directly.
This is a deliberate tradeoff — `fromArray()`/`toArray()` gymnastics are eliminated
at the cost of PSR convention. Do not "correct" this to camelCase.

---

## Dependency Direction

```
Domain        → nothing (plain PHP, no framework imports)
Application   → Domain only
Infrastructure → Domain interfaces (implements them), Laravel internals allowed
Http          → Application (use cases, commands), Laravel internals allowed
```

The Domain layer is the strict boundary. Everything else may use Laravel freely.

---

## Adding a New Feature — Checklist

Follow this order when implementing any new feature:

1. **Domain first** — does it need a new entity or value object? Define it in `app/Domain/`.
2. **Repository contract** — if new persistence is needed, add a method to the relevant interface
   in `app/Domain/{Domain}/Contracts/`, or create a new interface.
3. **Repository implementation** — implement in `app/Infrastructure/Persistence/Eloquent/`.
4. **Register DI** — add the binding in `RepositoryServiceProvider`.
5. **Command** — create a `VerbNounCommand` DTO in `app/Application/Commands/`.
6. **Use case** — create the use case in `app/Application/UseCases/`.
7. **Domain event** — if the operation has side effects, create an event in
   `app/Domain/{Domain}/Events/` and register listeners in `EventServiceProvider`.
8. **Form Request** — create a `FormRequest` for input validation.
9. **Controller** — thin controller that calls the use case and returns Inertia response.
10. **Route** — register in `routes/web.php`.
11. **Frontend** — build or update the Svelte page/component. Use Inertia `router` for mutations,
    Echo client for real-time updates.
12. **Tests** — write a Pest feature test covering the happy path and key edge cases.

---

## Anti-Patterns to Avoid

- **Fat controllers** — no business logic in controllers, ever.
- **Eloquent in use cases** — use cases must not import or query Eloquent models directly.
- **Domain importing Laravel** — domain entities and value objects are plain PHP only.
- **Raw `fetch` for page data** — use Inertia deferred props instead.
- **Mutable value objects** — value objects that change state in place break the domain model.
- **Concrete repository injection** — always inject the interface, not the Eloquent implementation.
- **Repositories returning Eloquent models** — repositories return domain entities, full stop.
- **Child entity repositories** — `ClockEntryEntity` has no repository; load it through
  `DailyLogRepository`.
- **Cross-domain entity imports** — reference other domains by ID value objects only; the one
  exception is `app/Domain/Shared/ValueObjects/`.
- **`DB::` raw queries** — prefer the repository pattern; use query builder only for genuinely
  complex read-side queries, and keep those in dedicated query classes or read models.
- **`env()` outside config files** — use `config('key')` everywhere in application code.
- **Side effects in domain entities** — entities raise no events, call no services, touch no
  infrastructure; they compute and enforce invariants only.