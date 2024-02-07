<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackAfterLogout
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && !$request->session()->has('logout')) {
            return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                            ->header('Pragma', 'no-cache')
                            ->header('Expires', '0');
        }

        return $response;
    }
}
