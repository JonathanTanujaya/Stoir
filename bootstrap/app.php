<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'module' => \App\Http\Middleware\ModuleAccess::class,
            'api.rate_limit' => \App\Http\Middleware\ApiRateLimit::class,
            'sanitize' => \App\Http\Middleware\SanitizeInput::class,
            'log.requests' => \App\Http\Middleware\RequestLogger::class,
            'format.api' => \App\Http\Middleware\FormatApiResponse::class,
        ]);

        // Enable CORS for API routes
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Apply security middleware to all API routes
        $middleware->group('api', [
            'api.rate_limit:120,1', // 120 requests per minute
            'sanitize',
            'log.requests',
            'format.api', // Format API responses consistently
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
