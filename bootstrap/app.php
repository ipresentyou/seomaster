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
        $exceptions->render(function (\Illuminate\Routing\Exceptions\InvalidSignatureException $e, $request) {
            if ($request->routeIs('trial-feedback.*')) {
                return response('Dieser Feedbacklink ist ungültig oder abgelaufen. Bitte kontaktiere uns über shopware-seomaster.com/kontakt.', 403)
                    ->header('Content-Type', 'text/plain; charset=UTF-8')
                    ->header('X-Robots-Tag', 'noindex, nofollow');
            }
            if ($request->routeIs('verification.verify')) {
                return redirect()->route('verification.notice')
                    ->with('status', 'verification-link-invalid');
            }

            return redirect()->route('login')
                ->with('error', '⚠️ Dieser Link ist abgelaufen oder ungültig. Bitte fordere einen neuen an.');
        });

        $exceptions->render(function (\RuntimeException $e, $request) {
            if (str_contains($e->getMessage(), 'KI-Credentials')) {
                return redirect()->route('credentials.index')
                    ->with('error', '⚠️ ' . $e->getMessage());
            }
        });
        //
    })
    ->create();
