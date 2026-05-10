<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TaskCategoryAlias extends Model
{
    use UsesTenantConnection;
}
