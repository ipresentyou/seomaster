<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Append onboarding check to all web routes ────────────────────────
        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\RedirectIfOnboardingIncomplete::class,
        ]);

        // ── Route Middleware Aliases ─────────────────────────────────────────
        $middleware->alias([
            'user.active'           => \App\Http\Middleware\EnsureUserIsActive::class,
            'subscription.active'   => \App\Http\Middleware\EnsureSubscriptionIsActive::class,
            'onboarding'            => \App\Http\Middleware\RedirectIfOnboardingIncomplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\RuntimeException $e, $request) {
            if (str_contains($e->getMessage(), 'KI-Credentials')) {
                return redirect()->route('credentials.index')
                    ->with('error', '⚠️ ' . $e->getMessage());
            }
        });
        //
    })
    ->create();
