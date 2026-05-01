<?php

namespace App\Concerns;

use App\Models\GoogleConnection;

trait HasGoogleConnection
{
    public function googleConnection()
    {
        return $this->hasOne(GoogleConnection::class);
    }

    public function hasGoogleConnection(): bool
    {
        return $this->googleConnection !== null;
    }
}
