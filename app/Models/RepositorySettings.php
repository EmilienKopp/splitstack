<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RepositorySettings extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'repository_id',
        'repository',
        'branch',
        'excluded_folders',
        'excluded_extensions',
        'include_diff_by_default',
    ];

    protected $casts = [
        'excluded_folders' => 'array',
        'excluded_extensions' => 'array',
        'include_diff_by_default' => 'boolean',
    ];
}
