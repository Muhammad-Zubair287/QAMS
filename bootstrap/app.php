<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register 'role' as a route middleware alias
        // Usage: Route::middleware('role:admin')
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // When an authenticated user tries to access a 'guest' route (e.g. /login),
        // redirect them to their correct dashboard instead of '/' (which would loop).
        $middleware->redirectUsersTo(function () {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user === null) {
                return route('login');
            }
            $map = [
                'admin'   => 'admin.dashboard',
                'teacher' => 'teacher.dashboard',
                'student' => 'student.dashboard',
            ];
            return route($map[$user->role] ?? 'login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
