<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AllowedRolesMiddleware; ////////////////
use App\Http\Middleware\AccessMiddleware; ////////////////

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => AllowedRolesMiddleware::class, 
            'access' => AccessMiddleware::class,
        ]); ///////////////////

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

    $app->withMiddleware([
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
    
