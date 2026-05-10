<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use App\Domain\TimeTracking\Entities\ClockEntryEntity;
use App\Models\Concerns\HasEntity;
use Carbon\Carbon;
use Database\Factories\ClockEntryFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ClockEntry extends Model
{
    /** @use HasFactory<ClockEntryFactory> */
    use HasEntity, HasFactory, UsesTenantConnection;

    protected $fillable = [
        'daily_log_id',
        'client_id',
        'in',
        'out',
        'notes',
        'timezone',
    ];

    protected $casts = [
        'in' => 'datetime',
        'out' => 'datetime',
    ];

    protected $appends = [];

    public static function booted()
    {
        static::saving(function ($clockEntry) {
            if (! $clockEntry->timezone) {
                $clockEntry->timezone = $clockEntry->user->timezone ?? config('app.timezone');
            }
        });

        static::saved(function ($clockEntry) {
            DailyLog::recomputeTotalSeconds($clockEntry->daily_log_id);
        });
    }

    #[ExportRelationship(ActivityLog::class, type: 'hasMany')]
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function dailyLog()
    {
        return $this->belongsTo(DailyLog::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('in', now());
    }

    public function scopeOwn($query, $userOrId = null)
    {
        if (! $userOrId) {
            $userOrId = Auth::user();
        } elseif ($userOrId instanceof User) {
            $userOrId = $userOrId->id;
        }

        return $query->where('user_id', $userOrId);
    }

    protected function inTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)
                ->setTimezone($this->timezone)
                ->format('H:i:s'),
        );
    }

    protected function outTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)
                ->setTimezone($this->timezone)
                ->format('H:i:s'),
        );
    }

    protected function in(): Attribute
    {
        if (! isset($this->timezone)) {
            $this->timezone = config('app.timezone');
        }

        return Attribute::make(
            get: fn ($value) => isset($value)
            ? Carbon::parse($value)
                ->setTimezone($this->timezone)
            : null,
        );
    }

    protected function out(): Attribute
    {
        if (! isset($this->timezone)) {
            $this->timezone = config('app.timezone');
        }

        return Attribute::make(
            get: fn ($value) => isset($value)
            ? Carbon::parse($value)
                ->setTimezone($this->timezone)
            : null,
        );
    }

    protected function date(): Attribute
    {
        if (! $this->timezone) {
            $this->timezone = config('app.timezone');
        }

        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)
                ->setTimezone($this->timezone)
                ->format('Y-m-d'),
        );
    }

    public function toEntity(): ClockEntryEntity
    {
        return new ClockEntryEntity(
            id: $this->id,
            daily_log_id: $this->daily_log_id,
            in: $this->in,
            out: $this->out,
            timezone: $this->timezone,
            notes: $this->notes,
        );
    }
}
