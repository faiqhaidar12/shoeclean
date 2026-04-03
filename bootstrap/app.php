<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        // Exclude payment notification from CSRF verification (for Midtrans & Mayar webhooks)
        $middleware->validateCsrfTokens(except: [
            'payment/notification',
            'webhook/mayar',
            'webhook/duitku',
            'api/auth/register',
            'api/auth/login',
            'api/auth/logout',
            'api/customers',
            'api/customers/*',
            'api/orders/*',
            'api/services',
            'api/services/*',
            'api/expenses',
            'api/expenses/*',
            'api/promos',
            'api/promos/*',
            'api/outlets',
            'api/outlets/*',
            'api/surveys',
            'api/surveys/*',
            'api/superadmin/*',
            'api/team',
            'api/team/*',
            'api/subscription/checkout/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
