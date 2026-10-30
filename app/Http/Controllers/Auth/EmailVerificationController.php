<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice(): View|\Illuminate\Http\RedirectResponse
    {
        if (request()->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.verify-email');
    }

    /**
     * Mark email as verified from the signed URL in the email.
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403);
        }

        // Signierter Link aus dem eigenen Postfach → direkt einloggen,
        // statt den User auf die Login-Seite zu schicken
        if (! Auth::check() || Auth::id() !== $user->id) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard') . '?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('dashboard') . '?verified=1')
            ->with('success', '✅ E-Mail erfolgreich bestätigt! Willkommen bei SEOmaster.');
    }

    /**
     * Resend the verification email.
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
