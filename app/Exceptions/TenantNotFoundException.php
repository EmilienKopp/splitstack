<?php

namespace App\Exceptions;

use Exception;

class TenantNotFoundException extends Exception
{
    public function render($request)
    {
        return redirect()->route('register-on-the-fly');
    }
}
