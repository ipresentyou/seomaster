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
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms'    => ['required', 'accepted'],
        ], [
            'name.required'     => 'Bitte gib deinen Namen ein.',
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
                if (!$existingUser->subscription) {
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
}
