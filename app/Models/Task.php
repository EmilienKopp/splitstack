<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, UsesTenantConnection;

    protected $guarded = [];

    #[ExportRelationship(Project::class)]
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
