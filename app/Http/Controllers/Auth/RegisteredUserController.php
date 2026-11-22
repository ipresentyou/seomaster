<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /** Mindestzeit zwischen Formularaufruf und Absenden (Bot-Schutz). */
    private const MIN_FILL_SECONDS = 3;

    public function create(Request $request): View
    {
        $request->session()->put('register_form_at', now()->getTimestamp());

        return view('auth.register');
    }

    public function store(Request $request)
    {
        if ($this->looksLikeBot($request)) {
            Log::info('Registrierung als Bot verworfen', [
                'ip'    => $request->ip(),
                'email' => $request->input('email'),
                'name'  => mb_substr((string) $request->input('name'), 0, 100),
            ]);

            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors(['email' => 'Die Registrierung konnte nicht abgeschlossen werden. Bitte lade die Seite neu und versuche es erneut.']);
        }

        $request->validate([
            'name'     => [
                'required', 'string', 'max:100',
                // Nur Buchstaben, Leerzeichen, Punkt, Apostroph, Bindestrich
                'regex:/^[\pL\pM][\pL\pM\s.\'’-]*$/u',
                // Keine Domains/Links wie "forms.gle" oder "example.com"
                'not_regex:/[\pL\d-]\.[a-z]{2,}\b/iu',
            ],
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms'    => ['required', 'accepted'],
        ], [
            'name.required'     => 'Bitte gib deinen Namen ein.',
            'name.max'          => 'Der Name darf höchstens 100 Zeichen lang sein.',
            'name.regex'        => 'Bitte gib nur deinen Namen ein (Buchstaben, keine Zahlen, Links oder Sonderzeichen).',
            'name.not_regex'    => 'Bitte gib nur deinen Namen ein – Links und Domains sind nicht erlaubt.',
            'email.required'    => 'Bitte gib deine E-Mail-Adresse ein.',
            'password.required' => 'Bitte wähle ein Passwort.',
            'password.confirmed'=> 'Die Passwörter stimmen nicht überein.',
            'terms.required'    => 'Bitte akzeptiere die AGB.',
            'terms.accepted'    => 'Bitte akzeptiere die AGB.',
        ]);

        // Check ob ein soft-deleted User existiert
        $existingUser = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if ($existingUser) {
            if ($existingUser->trashed()) {
                // User wiederherstellen
                $existingUser->restore();
                $existingUser->update([
                    'name'     => $request->name,
                    'password' => Hash::make($request->password),
                    'status'   => 'active',
                ]);

                // Subscription prüfen/wiederherstellen
                if (! $existingUser->subscriptions()->exists()) {
                    $plan = SubscriptionPlan::where('slug', 'starter')->first();
                    if ($plan) {
                        Subscription::create([
                            'user_id'              => $existingUser->id,
                            'subscription_plan_id' => $plan->id,
                            'billing_cycle'        => 'monthly',
                            'status'               => 'trial',
                            'trial_ends_at'        => now()->addDays(3),
                        ]);
                    }
                }

                event(new Registered($existingUser));
                Auth::login($existingUser);

                return redirect()->route('verification.notice')
                    ->with('success', '✅ Willkommen zurück! Dein Account wurde wiederhergestellt.');
            } else {
                // User existiert und ist nicht gelöscht
                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Diese E-Mail-Adresse ist bereits registriert.']);
            }
        }

        // Neuer User erstellen
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        // Auto-start 3-day trial on the starter plan
        $plan = SubscriptionPlan::where('slug', 'starter')->first();
        if ($plan) {
            Subscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle'        => 'monthly',
                'status'               => 'trial',
                'trial_ends_at'        => now()->addDays(3),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    /** Honeypot ausgefüllt oder Formular zu schnell / ohne vorherigen Aufruf abgeschickt. */
    private function looksLikeBot(Request $request): bool
    {
        if (filled($request->input('website'))) {
            return true;
        }

        $startedAt = $request->session()->pull('register_form_at');
        if (! is_int($startedAt)) {
            return true;
        }

        return now()->getTimestamp() - $startedAt < self::MIN_FILL_SECONDS;
    }
}
