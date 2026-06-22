<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Broadcast;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        // ✅ REMOVED channels: — we register manually below to control middleware
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Stack both guards so web users (session) AND mobile users (Bearer token)
            // can both authenticate private channels.
            Broadcast::routes(['middleware' => ['web', 'auth:web,sanctum']]);

            require base_path('routes/channels.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'broadcasting/auth', // Required — mobile apps don't send CSRF tokens
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        $middleware->alias([
            'mentor'     => \App\Http\Middleware\MentorMiddleware::class,
            'mentor.api' => \App\Http\Middleware\EnsureMentorApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();