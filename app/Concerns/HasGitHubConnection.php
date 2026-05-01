<?php

namespace App\Concerns;

use App\Models\GitHubConnection;

trait HasGitHubConnection
{
    public function gitHubConnection()
    {
        return $this->hasOne(GitHubConnection::class);
    }

    public function hasGitHubConnection(): bool
    {
        return $this->gitHubConnection !== null;
    }
}
