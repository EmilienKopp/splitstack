<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class SplitMake extends Command
{
    protected $signature = 'split:make
                            {name? : Entity name (StudlyCase). Triggers backend domain scaffolding.}
                            {--entity-name= : Override the entity class name (default: {Name}Entity)}
                            {--repository-name= : Override the repository base name (default: {Name}RepositoryInterface)}
                            {--model : Create an Eloquent model}
                            {--migration : Create a database migration}
                            {--repository : Create a repository interface and implementation}
                            {--actions : Create Create, Update, and Delete actions in app/Actions/}
                            {--queries : Create List and Get queries in app/Queries/}
                            {--usecases : Create read and write use cases in app/UseCases/}
                            {--all : Create all backend artifacts (entity, model, migration, repository, use cases)}';

    protected $description = 'Scaffold clean architecture boilerplate: entities, repositories, actions, queries, and use cases';

    public function handle(): void
    {
        intro('  split:make  ·  Clean Architecture Scaffolder');

        $name = $this->argument('name');

        if ($name) {
            $this->handleBackend(Str::studly($name));
        } else {
            $this->handleInteractive();
        }

        outro('Scaffolding complete.');
    }

    private function handleInteractive(): void
    {
        $mode = select(
            label: 'What do you want to scaffold?',
            options: [
                'backend' => 'Backend domain  (entity, repository, use cases…)',
                'frontend' => 'Frontend file   (component, page, layout, util)',
            ],
        );

        if ($mode === 'backend') {
            $rawName = text(
                label: 'Entity name',
                placeholder: 'e.g. Product, BlogPost, OrderItem',
                validate: fn (string $v) => blank(trim($v)) ? 'Entity name is required.' : null,
            );
            $this->handleBackend(Str::studly($rawName));
        } else {
            $this->handleFrontend();
        }
    }

    // ─── Backend ────────────────────────────────────────────────────────────────

    private function handleBackend(string $name): void
    {
        $entityName = $this->option('entity-name')
            ? Str::studly($this->option('entity-name'))
            : $name;

        $repositoryName = $this->option('repository-name')
            ? Str::studly($this->option('repository-name'))
            : $name;

        $all = $this->option('all');
        $hasArtifactFlags = $all
            || $this->option('model')
            || $this->option('migration')
            || $this->option('repository')
            || $this->option('actions')
            || $this->option('queries')
            || $this->option('usecases');

        if (! $hasArtifactFlags) {
            if (! $this->option('entity-name') && ! $this->option('repository-name')) {
                if (confirm(label: 'Override entity or repository class names?', default: false)) {
                    $entityName = Str::studly(text(
                        label: 'Entity class name',
                        default: $entityName,
                        hint: "Generates {$entityName}Entity",
                    ));
                    $repositoryName = Str::studly(text(
                        label: 'Repository base name',
                        default: $repositoryName,
                        hint: "Generates {$repositoryName}RepositoryInterface",
                    ));
                }
            }

            $selected = multiselect(
                label: "Artifacts to generate for {$name}",
                options: [
                    'model' => 'Eloquent model',
                    'migration' => 'Database migration',
                    'repository' => 'Repository  (interface + implementation)',
                    'actions' => 'CQS Actions  (Create, Update, Delete)  →  app/Actions/',
                    'queries' => 'CQS Queries  (List, Get)  →  app/Queries/',
                    'usecases' => 'Use Cases  (all operations)  →  app/UseCases/',
                ],
                default: ['model', 'migration', 'repository', 'usecases'],
                required: true,
            );

            $generateModel = in_array('model', $selected);
            $generateMigration = in_array('migration', $selected);
            $generateRepository = in_array('repository', $selected);
            $generateActions = in_array('actions', $selected);
            $generateQueries = in_array('queries', $selected);
            $generateUseCases = in_array('usecases', $selected);
        } else {
            $generateModel = $all || (bool) $this->option('model');
            $generateMigration = $all || (bool) $this->option('migration');
            $generateRepository = $all || (bool) $this->option('repository');
            $generateActions = (bool) $this->option('actions');
            $generateQueries = (bool) $this->option('queries');
            $generateUseCases = $all || (bool) $this->option('usecases');
        }

        $this->generateEntity($entityName);

        if ($generateModel) {
            $this->generateModel($name);
        }

        if ($generateMigration) {
            $this->generateMigration($name);
        }

        if ($generateRepository) {
            $this->generateRepositoryInterface($repositoryName, $entityName);
            $this->generateRepository($repositoryName, $entityName);
        }

        if ($generateActions) {
            $this->generateAction($name, $entityName, $repositoryName, 'create');
            $this->generateAction($name, $entityName, $repositoryName, 'update');
            $this->generateAction($name, $entityName, $repositoryName, 'delete');
        }

        if ($generateQueries) {
            $this->generateQuery($name, $entityName, $repositoryName, 'index');
            $this->generateQuery($name, $entityName, $repositoryName, 'show');
        }

        if ($generateUseCases) {
            $this->generateUseCase($name, $entityName, $repositoryName, 'create');
            $this->generateUseCase($name, $entityName, $repositoryName, 'update');
            $this->generateUseCase($name, $entityName, $repositoryName, 'delete');
            $this->generateUseCase($name, $entityName, $repositoryName, 'index');
            $this->generateUseCase($name, $entityName, $repositoryName, 'show');
        }
    }

    private function generateEntity(string $name): void
    {
        $path = app_path("Domain/Entities/{$name}Entity.php");

        if (File::exists($path)) {
            warning("Entity already exists: app/Domain/Entities/{$name}Entity.php");

            return;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->renderStub('entity', $name));
        info("Entity created: app/Domain/Entities/{$name}Entity.php");
    }

    private function generateModel(string $name): void
    {
        spin(fn () => Artisan::call('make:model', ['name' => $name]), "Creating model {$name}…");
        info("Model created: app/Models/{$name}.php");
    }

    private function generateMigration(string $name): void
    {
        $table = Str::snake(Str::plural($name));
        spin(
            fn () => Artisan::call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]),
            "Creating migration for table '{$table}'…",
        );
        info("Migration created for table: {$table}");
    }

    private function generateRepositoryInterface(string $repositoryName, string $entityName): void
    {
        $path = app_path("Infrastructure/Contracts/{$repositoryName}RepositoryInterface.php");

        if (File::exists($path)) {
            warning("Repository interface already exists: app/Infrastructure/Contracts/{$repositoryName}RepositoryInterface.php");

            return;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->renderStub('repository-interface', $repositoryName, ['entity' => $entityName]));
        info("Repository interface created: app/Infrastructure/Contracts/{$repositoryName}RepositoryInterface.php");
    }

    private function generateRepository(string $repositoryName, string $entityName): void
    {
        $path = app_path("Infrastructure/Repositories/{$repositoryName}Repository.php");

        if (File::exists($path)) {
            warning("Repository already exists: app/Infrastructure/Repositories/{$repositoryName}Repository.php");

            return;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->renderStub('repository', $repositoryName, ['entity' => $entityName]));
        info("Repository created: app/Infrastructure/Repositories/{$repositoryName}Repository.php");
    }

    private function generateAction(string $name, string $entityName, string $repositoryName, string $type): void
    {
        $this->generateClassFile('Actions', $name, $entityName, $repositoryName, $type);
    }

    private function generateQuery(string $name, string $entityName, string $repositoryName, string $type): void
    {
        $this->generateClassFile('Queries', $name, $entityName, $repositoryName, $type);
    }

    private function generateUseCase(string $name, string $entityName, string $repositoryName, string $type): void
    {
        $this->generateClassFile('UseCases', $name, $entityName, $repositoryName, $type);
    }

    private function generateClassFile(string $baseDir, string $name, string $entityName, string $repositoryName, string $type): void
    {
        $plural = Str::plural($name);

        $classMap = [
            'create' => "Create{$name}",
            'update' => "Update{$name}",
            'delete' => "Delete{$name}",
            'index' => "List{$plural}",
            'show' => "Get{$name}",
        ];

        $stubMap = [
            'create' => 'action-create',
            'update' => 'action-update',
            'delete' => 'action-delete',
            'index' => 'query-index',
            'show' => 'query-show',
        ];

        $className = $classMap[$type];
        $path = app_path("{$baseDir}/{$name}/{$className}.php");

        if (File::exists($path)) {
            warning("Already exists: app/{$baseDir}/{$name}/{$className}.php");

            return;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->renderStub($stubMap[$type], $entityName, [
            'class' => $className,
            'namespace' => "App\\{$baseDir}\\{$name}",
            'repo' => $repositoryName,
            'lower' => lcfirst($repositoryName),
            'classes' => Str::plural($entityName),
        ]));
        info("Created: app/{$baseDir}/{$name}/{$className}.php");
    }

    // ─── Frontend ───────────────────────────────────────────────────────────────

    private function handleFrontend(): void
    {
        $type = select(
            label: 'What do you want to generate?',
            options: [
                'component' => 'Component  →  resources/js/components/',
                'page' => 'Page       →  resources/js/pages/',
                'layout' => 'Layout     →  resources/js/layouts/',
                'util' => 'Utility    →  resources/js/lib/utils/',
            ],
        );

        $name = text(
            label: 'File name',
            placeholder: 'e.g. UserCard, auth/Login',
            hint: 'Supports subdirectory paths (e.g. auth/Login)',
            validate: fn (string $v) => blank(trim($v)) ? 'File name is required.' : null,
        );

        match ($type) {
            'component' => $this->generateFrontendFile($name, 'components', 'svelte'),
            'page' => $this->generateFrontendFile($name, 'pages', 'svelte'),
            'layout' => $this->generateFrontendFile($name, 'layouts', 'svelte'),
            'util' => $this->generateFrontendFile($name, 'lib/utils', 'ts'),
        };
    }

    private function generateFrontendFile(string $name, string $dir, string $ext): void
    {
        $parts = collect(explode('/', $name));
        $filename = $parts->last();
        $subdir = $parts->count() > 1 ? $parts->slice(0, -1)->implode('/').'/' : '';

        $fullDir = resource_path("js/{$dir}/{$subdir}");
        File::ensureDirectoryExists($fullDir);

        $path = "{$fullDir}{$filename}.{$ext}";
        $relativePath = ltrim(str_replace(base_path(), '', $path), '/');
        File::put($path, '');
        info("Created: {$relativePath}");
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function renderStub(string $stub, string $name, array $extra = []): string
    {
        $content = File::get(base_path("stubs/{$stub}.stub"));

        $replacements = [
            'class' => $name,
            'entity' => $name,
            'repo' => $name,
            'lower' => lcfirst($name),
            'snake' => Str::snake($name),
            'plural' => Str::plural($name),
            ...$extra,
        ];

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }

        return $content;
    }
}
