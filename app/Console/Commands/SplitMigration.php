<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

final class SplitMigration extends Command
{
    protected $signature = 'split:migration
                            {model : Model class (e.g. Post or App\\Models\\Post)}';

    protected $description = "Scaffold a BM25-ready migration derived from a model's searchableColumns()";

    public function handle(): int
    {
        intro('  split:migration  ·  BM25 Migration Scaffolder');

        $modelClass = $this->resolveModelClass($this->argument('model'));

        if (! class_exists($modelClass)) {
            error(sprintf('Model [%s] not found.', $modelClass));

            return self::FAILURE;
        }

        $model = new $modelClass;

        if (! method_exists($model, 'searchableColumns')) {
            error(sprintf('[%s] must define searchableColumns() to scaffold a BM25 migration.', $modelClass));

            return self::FAILURE;
        }

        $columns = $model->searchableColumns();

        if (empty($columns)) {
            error(sprintf('[%s]::searchableColumns() returned an empty array.', $modelClass));

            return self::FAILURE;
        }

        $table = $model->getTable();

        $indexes = implode("\n", array_map(
            fn ($col): string => sprintf("        DB::statement(\"CREATE INDEX %s_%s_bm25 ON %s USING bm25(%s) WITH (text_config='{\$textConfig}')\");", $table, $col, $table, $col),
            $columns
        ));

        $dropIndexes = implode("\n", array_map(
            fn ($col): string => sprintf("        DB::statement('DROP INDEX IF EXISTS \"%s_%s_bm25\"');", $table, $col),
            $columns
        ));

        $content = str_replace(
            ['{{ table }}', '{{ indexes }}', '{{ drop_indexes }}'],
            [$table, $indexes, $dropIndexes],
            File::get(base_path('stubs/bm25-migration.stub'))
        );

        $filename = now()->format('Y_m_d_His').sprintf('_create_%s_table.php', $table);
        File::put(database_path('migrations/'.$filename), $content);

        info('Migration created: database/migrations/'.$filename);
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
