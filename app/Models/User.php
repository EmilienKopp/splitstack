<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasFullTextSearch;
use App\Concerns\HasGitHubConnection;
use App\Concerns\HasGoogleConnection;
use App\Concerns\HasTeams;
use App\Domain\Identity\Entities\UserEntity;
use App\Domain\Shared\ValueObjects\ID;
use App\Models\Concerns\HasEntity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Pennant\Concerns\HasFeatures;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Splitstack\Translucid\Concerns\HasTranslucid;

#[Hidden(['workos_id', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasEntity, HasFactory, HasFeatures, HasFullTextSearch, HasGitHubConnection,
        HasGoogleConnection, HasTeams, HasTranslucid, Notifiable, UsesTenantConnection;

    protected $guarded = [
        'org_id',
        'id',
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
            'id' => ID::class,
            'org_id' => ID::class,
            'workos_id' => ID::class,
        ];
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function toEntity(): UserEntity
    {
        return UserEntity::fromArray($this->toArray());
    }
}
