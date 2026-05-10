<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(OrganizationUser::class)
            ->withPivot('elevated')
            ->withTimestamps();
    }

    #[ExportRelationship(Project::class, type: 'hasMany')]
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
