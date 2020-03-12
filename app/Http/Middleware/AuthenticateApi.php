<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('api')->guest()) {
            return response()->json(['message' => 'Unauthorized Access.'], 401);
        }

        return $next($request);
    }
}
