<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Some code ...
     */


    protected $routeMiddleware = [
        // 'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.jwt' => \App\Http\Middleware\AuthenticateApi::class, // <= Add this
        // Other middlewares ...
    ];

    /**
     * Some code ...
     */
}
