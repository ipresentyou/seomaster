<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects authenticated users who haven't completed onboarding
 * to the wizard — unless they're already on an allowed route.
 */
class RedirectIfOnboardingIncomplete
{
    /**
     * Routes the middleware will NOT redirect away from.
     * Includes auth routes, onboarding itself, webhooks, admin panel.
     */
    private const ALLOWED_ROUTE_PREFIXES = [
        'onboarding',
        'login',
        'logout',
        'register',
        'password',
        'verification',
        'email',
        'filament',
        'admin',
        'webhooks',
        'livewire',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply to authenticated users
        if (! $user) {
            return $next($request);
        }

        // Admins skip onboarding
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Already completed
        if ($user->hasCompletedOnboarding()) {
            return $next($request);
        }

        // Check if current route is allowed (don't redirect in infinite loop)
        $routeName = $request->route()?->getName() ?? '';

        foreach (self::ALLOWED_ROUTE_PREFIXES as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return $next($request);
            }
        }

        // Also allow API/AJAX requests through
        if ($request->expectsJson() || $request->is('api/*')) {
            return $next($request);
        }

        // Redirect to current onboarding step
        $step = $user->onboarding_step ?? 1;
        return redirect()->route('onboarding.step', $step);
    }
}
