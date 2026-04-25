<?php

namespace App\Concerns;

use App\Search\PgTextSearchEngine;
use Laravel\Scout\Searchable;

/**
 * Wires a model to the pg_textsearch Scout engine.
 *
 * Each column returned by searchableColumns() MUST have a BM25 index:
 *   CREATE INDEX {table}_{column}_bm25 ON {table} USING bm25({column})
 *     WITH (text_config='english');
 *
 * Usage:
 *   use HasFullTextSearch;
 *
 *   public function searchableColumns(): array
 *   {
 *       return ['title', 'body'];
 *   }
 *
 * If searchableColumns() is not defined, the keys of toSearchableArray() are
 * used. For multi-column models the engine sums BM25 scores across all columns.
 *
 * Models that should use Scout's built-in database engine instead should use
 * Laravel\Scout\Searchable directly.
 */
trait HasFullTextSearch
{
    use Searchable;

    public function searchableUsing(): PgTextSearchEngine
    {
        return app(PgTextSearchEngine::class);
    }
}
