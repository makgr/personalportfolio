<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // The API is stateless: the Next.js frontend sends no cookies, so Sanctum's
        // stateful middleware would only demand a CSRF token it never has (419 on POST).
        // Protected routes authenticate with Sanctum bearer tokens instead.

        // Configure rate limiting for API
        // $middleware->throttleApi();

        // Set redirect after login (optional)
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/home');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        });
    })->create();
