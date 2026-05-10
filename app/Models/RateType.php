<?php

namespace App\Models;

use App\Enums\RateTypeScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RateType extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'name',
        'description',
        'scope',
        'organization_id',
        'project_id',
        'user_id',
    ];

    protected $casts = [
        'scope' => RateTypeScope::class,
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
