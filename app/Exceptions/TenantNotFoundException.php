<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Context;

class TenantNotFoundException extends Exception
{
    public function render($request)
    {
        $user = Context::get('user');

        return inertia()->render('RegisterOnTheFly', [
            'showSupportContact' => true,
            'errors' => ['Tenant not found. Please contact support.'],
            'user' => $user,
        ])
            ->toResponse($request);
    }
}
