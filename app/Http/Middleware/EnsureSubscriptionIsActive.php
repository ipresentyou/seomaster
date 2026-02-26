<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stellt sicher, dass der User ein aktives Abo hat (oder Admin ist).
 */
class EnsureSubscriptionIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if (! $user->hasActiveSubscription()) {
            return redirect()->route('subscription.index')
                ->with('warning', 'Du benötigst ein aktives Abonnement, um SEO-Tools zu nutzen.');
        }

        return $next($request);
    }
}
