<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Repository extends Model
{
    use UsesTenantConnection;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
