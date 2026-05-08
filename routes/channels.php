<?php

use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('translucid.{space}', function ($user, $space) {
    return Tenant::current()?->space === $space;
});
