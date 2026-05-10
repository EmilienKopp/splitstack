<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GitLog extends Model
{
    /** @use HasFactory<\Database\Factories\GitLogFactory> */
    use HasFactory, UsesTenantConnection;
}
