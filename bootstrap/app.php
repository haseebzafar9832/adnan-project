<?php

use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\CloseExpiredGroups;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminOnly::class,
            'desk' => \App\Http\Middleware\KeepRolesApart::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            return $request->user()?->isAdmin()
                ? route('admin.dashboard')
                : route('dashboard');
        });

        $middleware->web(append: [
            EnsureUserIsActive::class,
            CloseExpiredGroups::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
            'current_password',
            'card_number',
            'cvv',
        ]);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
