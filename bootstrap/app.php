<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RedirectUnauthenticated;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('diagnostics:cleanup')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if (!$request->expectsJson()) {
                $source = 'login';
                if ($request->is('password/email')) {
                    $source = 'email';
                } elseif ($request->is('password/reset')) {
                    $source = 'reset';
                }

                session()->flash('throttle_error', [
                    'seconds' => $e->getHeaders()['Retry-After'] ?? 60,
                    'source' => $source,
                ]);
                return response()->view('errors.429', [
                    'exception' => $e,
                ], 429);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            // If the request is for the API, return a proper 401 JSON response
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'detail' => 'No valid token provided. Please login first.'
                ], 401);
            }
            
            // Otherwise, keep your existing 403 behavior for Web
            return abort(403);
        });
    })->create();
