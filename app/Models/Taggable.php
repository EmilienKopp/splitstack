<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Taggable extends MorphPivot
{
    use UsesTenantConnection;
}
