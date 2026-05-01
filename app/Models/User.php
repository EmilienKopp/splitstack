<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasFullTextSearch;
use App\Concerns\HasGitHubConnection;
use App\Concerns\HasGoogleConnection;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Pennant\Concerns\HasFeatures;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

#[Fillable(['name', 'email', 'workos_id', 'avatar', 'current_team_id'])]
#[Hidden(['workos_id', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasFeatures, HasFullTextSearch, HasGitHubConnection,
        HasGoogleConnection, HasTeams, Notifiable, UsesTenantConnection;

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
        ];
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role')
            ->withTimestamps();
    }
}
