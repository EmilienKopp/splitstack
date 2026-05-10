<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Identity\Contracts\UserRepository;
use App\Domain\Identity\Entities\UserEntity;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Collection;

class EloquentUserRepository implements UserRepository
{
    public function with(array $relations): UserRepository
    {
        // This is a no-op for the local repository
        return $this;
    }

    public function find(int $id, $relations = null): ?UserEntity
    {
        if ($relations) {
            $model = User::with($relations)->find($id);

            return $model ? UserEntity::fromArray($model->toArray()) : null;
        }

        $model = User::find($id);

        return $model ? UserEntity::fromArray($model->toArray()) : null;
    }

    public function findByWorkosId(string $workosId): ?UserEntity
    {
        $user = User::where('workos_id', $workosId)->first();

        return $user ? UserEntity::fromArray($user->toArray()) : null;
    }

    public function findByGitHubId(string $githubUserId): ?UserEntity
    {
        return User::whereHas('gitHubConnection', function ($query) use ($githubUserId) {
            $query->where('github_user_id', $githubUserId);
        })->first();
    }

    public function findByGoogleId(string $googleUserId): ?User
    {
        return User::whereHas('googleConnection', function ($query) use ($googleUserId) {
            $query->where('google_user_id', $googleUserId);
        })->first();
    }

    public function findByEmail(string $email): ?UserEntity
    {
        return User::where('email', $email)->first();
    }

    public function getGitHubUserId(int $userId): ?string
    {
        $user = User::find($userId);

        return $user?->gitHubConnection?->github_user_id;
    }

    public function all(): Collection
    {
        return User::all();
    }

    public function save(UserEntity $user): UserEntity
    {
        return User::create($user->toArray())->toEntity();
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if (! $user) {
            return false;
        }

        return $user->delete();
    }

    // public function batchSetClockOutTime(User $user, ?DateTimeInterface $autoClockOutTime = null, ?DateTimeInterface $before = null): void
    // {
    //     $user->clockEntries()
    //         ->whereNull('out')
    //         ->when($before, fn ($query) => $query->where('in', '<=', $before))
    //         ->update(['out' => $autoClockOutTime]);
    // }
}
