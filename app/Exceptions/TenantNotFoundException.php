<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class TenantNotFoundException extends Exception
{
    public function render($request)
    {
        return redirect()->route('register-on-the-fly');
    }
}
