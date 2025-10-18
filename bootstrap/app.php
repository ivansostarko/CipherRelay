<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\ThreadSession;

return Application::configure(basePath: dirname(__DIR__))
    // Routing: load the standard web routes, console routes, and a health endpoint.
    // Then, mount the admin routes under the /admin prefix using the 'web' middleware group.
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('admin')
                ->middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )

    // Middleware: register aliases used by this app.
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth'      => AdminAuth::class,
            'thread.session'  => ThreadSession::class,
        ]);

        // Optional hardening examples:
        // $middleware->trustProxies(at: '*');
        // $middleware->trustHosts(at: ['^localhost$', '^.+\.yourdomain\.tld$']);
    })

    // Exceptions: customize as needed (left default here).
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();