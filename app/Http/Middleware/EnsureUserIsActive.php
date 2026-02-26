<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gesperrte User direkt abfangen.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isSuspended()) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Dein Account wurde gesperrt. Bitte kontaktiere den Support.']);
        }

        return $next($request);
    }
}
