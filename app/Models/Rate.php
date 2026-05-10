<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\RateFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Rate extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'rate_type_id',
        'rate_frequency',
        'organization_id',
        'project_id',
        'user_id',
        'amount',
        'currency',
        'overtime_multiplier',
        'holiday_multiplier',
        'special_multiplier',
        'custom_multiplier_rate',
        'custom_multiplier_label',
        'is_default',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'overtime_multiplier' => 'decimal:2',
        'holiday_multiplier' => 'decimal:2',
        'special_multiplier' => 'decimal:2',
        'custom_multiplier_rate' => 'decimal:2',
        'is_default' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'rate_frequency' => RateFrequency::class,
        'amount' => Money::class,
    ];

    public function scopeForProject($query, Project $project)
    {
        $project->loadMissing(['organization', 'users']);

        return $query->where('project_id', $project->id)
            ->orWhere('organization_id', $project->organization_id)
            ->orWhereIn('user_id', $project->users->pluck('id'));
    }

    public function rateType(): BelongsTo
    {
        return $this->belongsTo(RateType::class);
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

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('effective_until')
                ->orWhere('effective_until', '>', now());
        })->where(function ($q) {
            $q->whereNull('effective_from')
                ->orWhere('effective_from', '<=', now());
        });
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
