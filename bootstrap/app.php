<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(StartSession::class);
    $middleware->append(ShareErrorsFromSession::class);
    $middleware->append(\App\Http\Middleware\SetLocal::class);
         $middleware->alias([
            'admin' => AdminMiddleware::class, // <-- THIS LINE IS CRITICAL
            'auth' => \App\Http\Middleware\Authenticate::class, // Default Laravel 11 auth middleware
            // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
         ]);

})

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
