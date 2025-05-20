<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class Authorize
{
    public function handle($request, Closure $next, ...$modules)
    {
        if (! collect($modules)->reduce(fn ($result, $module) => $result && (Auth::user()->hasPermission($module)), true)) {

            throw new AuthorizationException;
        }

        return $next($request);
    }
}
