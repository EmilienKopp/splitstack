<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use App\Domain\TimeTracking\Entities\ProjectEntity;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Project extends BaseModel
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, Searchable, SoftDeletes, UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'metadata' => 'array',
    ];

    public function entityClass(): string
    {
        return ProjectEntity::class;
    }

    #[ExportRelationship(DailyLog::class, type: 'hasMany')]
    public function dailyLogs()
    {
        return $this->hasMany(DailyLog::class)->orderBy('date');
    }

    public function entries()
    {
        return $this->hasManyThrough(
            ClockEntry::class,
            DailyLog::class
        )->orderBy('start_time');
    }

    #[ExportRelationship(Task::class, type: 'hasMany')]
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    #[ExportRelationship(Organization::class)]
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(ProjectUser::class)
            ->withPivot('roles');
    }

    public function repositories()
    {
        return $this->hasMany(Repository::class);
    }
}
