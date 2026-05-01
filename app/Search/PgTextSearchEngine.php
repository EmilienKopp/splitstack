<?php

namespace App\Search;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\LazyCollection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

/**
 * Scout engine backed by pg_textsearch's <@> BM25 operator.
 *
 * BM25 indexes are created automatically on first search if they don't exist.
 * The index naming convention is {table}_{column}_bm25. You can also create
 * them explicitly via `php artisan scout:index "App\Models\YourModel"`.
 *
 * Models must define searchableColumns() to declare which columns are searched.
 * The text_config defaults to 'english' and can be overridden per model via
 * searchableTextConfig() or globally via SCOUT_PG_TEXTSEARCH_TEXT_CONFIG.
 *
 * For multi-column models the engine sums BM25 scores across all columns so
 * a hit in any column contributes to the final relevance rank.
 *
 * update/delete/flush are no-ops — the engine queries the live table directly.
 */
class PgTextSearchEngine extends Engine
{
    /** @var array<string, true> Tracks which table+column combos have been verified this process. */
    private static array $ensuredIndexes = [];

    public function update($models): void {}

    public function delete($models): void {}

    public function flush($model): void {}

    /**
     * Create BM25 indexes for all searchable columns on the given model.
     * Called by `php artisan scout:index "App\Models\Model"`.
     * $name is the model's searchableAs() value (typically the table name).
     */
    public function createIndex($name, array $options = []): mixed
    {
        $columns = $options['columns'] ?? [];
        $textConfig = $options['text_config'] ?? config('scout.pg_textsearch.text_config', 'english');
        $connection = $options['connection'] ?? null;

        if (! $connection || empty($columns)) {
            return [];
        }

        foreach ($columns as $column) {
            $indexName = "{$name}_{$column}_bm25";
            $this->createBm25Index($connection, $name, $column, $textConfig, $indexName);
        }

        return [];
    }

    public function deleteIndex($name): mixed
    {
        return [];
    }

    public function lazyMap(Builder $builder, $results, $model): LazyCollection
    {
        if ($results['total'] === 0) {
            return LazyCollection::empty();
        }

        $pk = $model->getKeyName();
        $ids = collect($results['hits'])->pluck($pk)->all();
        $positions = array_flip($ids);

        return $model->getScoutModelsByIds($builder, $ids)
            ->lazy()
            ->sortBy(fn ($m) => $positions[$m->getKey()] ?? PHP_INT_MAX);
    }

    public function search(Builder $builder): array
    {
        return $this->performSearch($builder);
    }

    public function paginate(Builder $builder, $perPage, $page): array
    {
        return $this->performSearch($builder, [
            'limit' => (int) $perPage,
            'offset' => ((int) $page - 1) * (int) $perPage,
        ]);
    }

    public function mapIds($results): SupportCollection
    {
        return collect($results['hits'])->pluck('id');
    }

    public function map(Builder $builder, $results, $model): Collection
    {
        if ($results['total'] === 0) {
            return $model->newCollection();
        }

        $pk = $model->getKeyName();
        $ids = collect($results['hits'])->pluck($pk)->all();
        $positions = array_flip($ids);

        return $model->getScoutModelsByIds($builder, $ids)
            ->sortBy(fn ($m) => $positions[$m->getKey()] ?? PHP_INT_MAX)
            ->values();
    }

    public function getTotalCount($results): int
    {
        return $results['total'];
    }

    private function performSearch(Builder $builder, array $options = []): array
    {
        $model = $builder->model;
        $connection = $model->getConnection();
        $table = $model->getTable();
        $pk = $model->getKeyName();
        $columns = $this->resolveColumns($model, $options);
        $limit = $options['limit'] ?? ($builder->limit ?? config('scout.pg_textsearch.limit', 3));
        $offset = $options['offset'] ?? 0;

        $indexNames = $this->ensureIndexesExist($model, $columns);
        $weights = $this->resolveWeights($model, $columns);
        $scoreExpr = $this->buildScoreExpression($columns, $table, $indexNames, $weights);
        [$whereSql, $whereBindings] = $this->compileWheres($builder);

        // Score bindings appear in the CTE SELECT clause, before the WHERE bindings.
        $scoreBindings = array_fill(0, count($columns), $builder->query);

        $cteSql = "WITH _scored AS (SELECT \"{$pk}\", {$scoreExpr} AS _score FROM \"{$table}\" {$whereSql})";

        $hits = $connection->select(
            "{$cteSql} SELECT \"{$pk}\" FROM _scored WHERE _score < 0 ORDER BY _score ASC LIMIT ? OFFSET ?",
            [...$scoreBindings, ...$whereBindings, $limit, $offset]
        );

        $total = (int) $connection->selectOne(
            "{$cteSql} SELECT COUNT(*) AS aggregate FROM _scored WHERE _score < 0",
            [...$scoreBindings, ...$whereBindings]
        )?->aggregate;

        return [
            'hits' => array_map(fn ($row) => (array) $row, $hits),
            'total' => $total,
        ];
    }

    /**
     * Resolve per-column priority weights for scoring.
     *
     * Resolution order:
     *   1. Model's searchableWeights(): array — explicit values
     *   2. Model's searchablePriorityScheme(): string — 'linear' | 'exponential'
     *   3. config('scout.pg_textsearch.priority_scheme') — global default ('linear')
     *
     * Linear (default): weights are N, N-1, …, 1 (first column wins most).
     * Exponential: weights are 1.0, 0.5, 0.25, … (sharp drop-off).
     *
     * @param  array<string>  $columns
     * @return array<float>
     */
    private function resolveWeights(mixed $model, array $columns): array
    {
        if (method_exists($model, 'searchableWeights')) {
            return $model->searchableWeights();
        }

        $scheme = method_exists($model, 'searchablePriorityScheme')
            ? $model->searchablePriorityScheme()
            : config('scout.pg_textsearch.priority_scheme', 'linear');

        $count = count($columns);

        return match ($scheme) {
            'exponential' => array_map(fn ($i) => 1.0 / (2 ** $i), range(0, $count - 1)),
            default => array_map(fn ($i) => (float) ($count - $i), range(0, $count - 1)),
        };
    }

    /**
     * Ensure BM25 indexes exist for all searchable columns.
     * Checks for any existing BM25 index on the column (regardless of name) to
     * avoid creating duplicates. Uses a static cache per process.
     *
     * @param  array<string>  $columns
     * @return array<string, string> map of column => resolved index name
     */
    private function ensureIndexesExist(mixed $model, array $columns): array
    {
        $table = $model->getTable();
        $connection = $model->getConnection();
        $textConfig = method_exists($model, 'searchableTextConfig')
            ? $model->searchableTextConfig()
            : config('scout.pg_textsearch.text_config', 'english');
        $indexNames = [];

        foreach ($columns as $column) {
            $cacheKey = "{$table}.{$column}";

            if (isset(self::$ensuredIndexes[$cacheKey])) {
                $indexNames[$column] = self::$ensuredIndexes[$cacheKey];

                continue;
            }

            $existing = $connection->selectOne(<<<'SQL'
                SELECT i.relname
                FROM pg_class t
                JOIN pg_index ix ON t.oid = ix.indrelid
                JOIN pg_class i ON i.oid = ix.indexrelid
                JOIN pg_am am ON am.oid = i.relam
                JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(ix.indkey)
                WHERE t.relname = ? AND am.amname = 'bm25' AND a.attname = ?
                LIMIT 1
                SQL, [$table, $column]);

            if ($existing) {
                $indexName = $existing->relname;
            } else {
                $indexName = "{$table}_{$column}_bm25";
                $this->createBm25Index($connection, $table, $column, $textConfig, $indexName);
            }

            self::$ensuredIndexes[$cacheKey] = $indexName;
            $indexNames[$column] = $indexName;
        }

        return $indexNames;
    }

    private function createBm25Index(mixed $connection, string $table, string $column, string $textConfig, string $indexName): void
    {
        $connection->statement(<<<SQL
            CREATE INDEX IF NOT EXISTS "{$indexName}"
            ON "{$table}" USING bm25("{$column}")
            WITH (text_config='{$textConfig}')
            SQL);
    }

    private function resolveColumns(mixed $model, array $options = []): array
    {
        $columnsArray = $options['columns'] ?? [];

        if (! method_exists($model, 'searchableColumns')) {
            throw new \InvalidArgumentException('Model must define searchableColumns() method when using pg_textsearch engine.');
        }

        $searchable = $model->searchableColumns();
        if (empty($searchable)) {
            throw new \InvalidArgumentException('Model\'s searchableColumns() method must return at least one column.');
        }

        if (empty($columnsArray)) {
            $columnsArray = $searchable;
        } else {
            foreach ($columnsArray as $col) {
                if (! in_array($col, $searchable)) {
                    throw new \InvalidArgumentException("Column '{$col}' is not declared in searchableColumns() for model ".get_class($model));
                }
            }
        }

        return $columnsArray;
    }

    /**
     * Build a BM25 score expression for use in a CTE.
     *
     * BM25 scores are negative — lower (more negative) = better match; 0.0 = no match.
     * Each column's score is multiplied by its priority weight before summing,
     * so hits in higher-priority columns dominate the final rank.
     *
     * @param  array<string>  $columns
     * @param  array<string, string>  $indexNames  column => index name
     * @param  array<float>  $weights  priority weights, index-aligned with $columns
     */
    private function buildScoreExpression(array $columns, string $table, array $indexNames, array $weights): string
    {
        $columns = array_values($columns);
        $parts = [];

        foreach ($columns as $i => $col) {
            $indexName = $indexNames[$col] ?? "{$table}_{$col}_bm25";
            $weight = $weights[$i] ?? 1.0;
            $parts[] = "COALESCE(\"{$col}\" <@> to_bm25query(?, '{$indexName}'), 0.0) * {$weight}";
        }

        return \count($parts) === 1
            ? $parts[0]
            : '('.implode(' + ', $parts).')';
    }

    /** @return array{string, array<mixed>} */
    private function compileWheres(Builder $builder): array
    {
        if (empty($builder->wheres)) {
            return ['', []];
        }

        $clauses = [];
        $bindings = [];

        foreach ($builder->wheres as $column => $value) {
            $clauses[] = "\"{$column}\" = ?";
            $bindings[] = $value;
        }

        return ['WHERE '.implode(' AND ', $clauses), $bindings];
    }
}
