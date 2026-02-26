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
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms'    => ['required', 'accepted'],
        ], [
            'name.required'     => 'Bitte gib deinen Namen ein.',
            'email.required'    => 'Bitte gib deine E-Mail-Adresse ein.',
            'email.unique'      => 'Diese E-Mail-Adresse ist bereits registriert.',
            'password.required' => 'Bitte wähle ein Passwort.',
            'password.confirmed'=> 'Die Passwörter stimmen nicht überein.',
            'terms.required'    => 'Bitte akzeptiere die AGB.',
            'terms.accepted'    => 'Bitte akzeptiere die AGB.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        // Auto-start 14-day trial on the starter plan
        $plan = SubscriptionPlan::where('slug', 'starter')->first();
        if ($plan) {
            Subscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle'        => 'monthly',
                'status'               => 'trial',
                'trial_ends_at'        => now()->addDays(14),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
