<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ModelHasRoles extends Model
{
    use UsesTenantConnection;

    protected $table = 'model_has_roles';
}
