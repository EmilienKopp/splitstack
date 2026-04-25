<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use Symfony\Component\HttpFoundation\Response;

class TenantHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request instanceof AuthKitAuthenticationRequest) {
            // Handle tenant finding and session storing here
            dd($request->all());
        }

        return $next($request);
    }
}
