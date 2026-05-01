<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

class SplitMigration extends Command
{
    protected $signature = 'split:migration
                            {model : Model class (e.g. Post or App\\Models\\Post)}';

    protected $description = 'Scaffold a BM25-ready migration derived from a model\'s searchableColumns()';

    public function handle(): int
    {
        intro('  split:migration  ·  BM25 Migration Scaffolder');

        $modelClass = $this->resolveModelClass($this->argument('model'));

        if (! class_exists($modelClass)) {
            error("Model [{$modelClass}] not found.");

            return self::FAILURE;
        }

        $model = new $modelClass;

        if (! method_exists($model, 'searchableColumns')) {
            error("[{$modelClass}] must define searchableColumns() to scaffold a BM25 migration.");

            return self::FAILURE;
        }

        $columns = $model->searchableColumns();

        if (empty($columns)) {
            error("[{$modelClass}]::searchableColumns() returned an empty array.");

            return self::FAILURE;
        }

        $table = $model->getTable();

        $indexes = implode("\n", array_map(
            fn ($col) => "        DB::statement(\"CREATE INDEX {$table}_{$col}_bm25 ON {$table} USING bm25({$col}) WITH (text_config='{\$textConfig}')\");",
            $columns
        ));

        $dropIndexes = implode("\n", array_map(
            fn ($col) => "        DB::statement('DROP INDEX IF EXISTS \"{$table}_{$col}_bm25\"');",
            $columns
        ));

        $content = str_replace(
            ['{{ table }}', '{{ indexes }}', '{{ drop_indexes }}'],
            [$table, $indexes, $dropIndexes],
            File::get(base_path('stubs/bm25-migration.stub'))
        );

        $filename = now()->format('Y_m_d_His')."_create_{$table}_table.php";
        File::put(database_path("migrations/{$filename}"), $content);

        info("Migration created: database/migrations/{$filename}");
        outro('Add your columns to the Schema::create callback, then run php artisan migrate.');

        return self::SUCCESS;
    }

    private function resolveModelClass(string $input): string
    {
        if (class_exists($input)) {
            return $input;
        }

        return 'App\\Models\\'.Str::studly($input);
    }
}
