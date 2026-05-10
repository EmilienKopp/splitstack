<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ActivityType extends Model
{
    use UsesTenantConnection;

    protected $fillable = ['name', 'description', 'color', 'icon'];

    #[ExportRelationship(ActivityLog::class, type: 'hasMany')]
    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
