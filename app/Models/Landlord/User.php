<?php

declare(strict_types=1);

namespace App\Models\Landlord;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasGitHubConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

final class User extends Authenticatable
{
    use HasFactory;
    use HasGitHubConnection;
    use Notifiable;
    use UsesLandlordConnection;

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
        ];
    }

    protected static function booted(): void
    {
        self::creating(function ($user): void {
            if (! $user->handle) {
                $user->handle = $user->email;
            }
        });
    }
}
