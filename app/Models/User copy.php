<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Attributes\ExportRelationship;
use App\Casts\N8nConfigCast;
use App\Concerns\HasGitHubConnection;
use App\Concerns\HasGoogleConnection;
use App\Models\Landlord\Tenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements OAuthenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasGitHubConnection, HasGoogleConnection, HasRoles, Notifiable, UsesTenantConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'handle',
        'title',
        'phone_main',
        'phone_secondary',
        'github',
        'email',
        'password',
        'workos_id',
        'n8n_config',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'n8n_config' => N8nConfigCast::class,
            'preferences' => 'array',
        ];
    }

    public static function booted()
    {
        static::creating(function ($user) {
            if (! $user->handle) {
                $user->handle = $user->email;
            }
            $user->assignRole('user');

        });

        static::created(function ($user) {
            DB::connection('landlord')->table('tenant_users')->insert([
                'tenant_id' => Tenant::current()->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class)
            ->using(OrganizationUser::class)
            ->withPivot('elevated')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Project>
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->using(ProjectUser::class);
    }

    #[ExportRelationship(ClockEntry::class, type: 'hasMany')]
    public function clockEntries()
    {
        return $this->hasManyThrough(
            ClockEntry::class,
            DailyLog::class,
            'user_id', // Foreign key on DailyLog table
            'daily_log_id', // Foreign key on ClockEntry table
            'id', // Local key on User table
            'id' // Local key on DailyLog table
        );
    }

    public function todaysEntries()
    {
        return $this->clockEntries()->today()->with(['dailyLog.project'])->orderBy('created_at', 'desc');
    }

    #[ExportRelationship(Report::class, type: 'hasMany')]
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    #[ExportRelationship(VoiceCommand::class, type: 'hasMany')]
    public function voiceCommands()
    {
        return $this->hasMany(VoiceCommand::class);
    }
}
