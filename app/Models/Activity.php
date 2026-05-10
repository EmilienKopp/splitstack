<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Activity extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_category_id',
        'date',
        'duration',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'duration' => 'integer',
    ];

    /**
     * Get the user that owns the activity.
     */
    #[ExportRelationship(User::class)]
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that the activity belongs to.
     */
    #[ExportRelationship(Project::class)]
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the task category that the activity belongs to.
     */
    #[ExportRelationship(TaskCategory::class)]
    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class);
    }
}
