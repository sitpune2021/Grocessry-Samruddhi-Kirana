<?php

use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\ApiAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'api.auth'   => ApiAuthenticate::class,
        ]);
    })

    ->withMiddleware(function ($middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
    })

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(false);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            AuthenticationException $e,
            $request
        ) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        });
    })

    ->create();
