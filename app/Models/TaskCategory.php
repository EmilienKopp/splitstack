<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use App\Traits\Aliasable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TaskCategory extends Model
{
    use Aliasable, UsesTenantConnection;

    protected $fillable = ['name', 'description'];

    #[ExportRelationship(Task::class, type: 'hasMany')]
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
