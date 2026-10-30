<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\TrialFeedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrialFeedbackController extends Controller
{
    public function show(Request $request, Subscription $subscription)
    {
        $this->ensureTrialEnded($subscription);

        return response()->view('feedback.trial', [
            'submitted' => TrialFeedback::where('subscription_id', $subscription->id)->exists(),
            'submitUrl' => $request->fullUrl(),
            'reasons' => TrialFeedback::REASONS,
        ])->header('X-Robots-Tag', 'noindex, nofollow')->header('Referrer-Policy', 'no-referrer');
    }

    public function store(Request $request, Subscription $subscription)
    {
        $this->ensureTrialEnded($subscription);
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'reason' => ['required', Rule::in(array_keys(TrialFeedback::REASONS))],
            'comment' => ['nullable', 'string', 'max:3000'],
        ]);

        // The unique subscription key also prevents duplicate submissions from parallel requests.
        TrialFeedback::query()->insertOrIgnore([
            ...$data,
            'subscription_id' => $subscription->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->to($request->fullUrl());
    }

    private function ensureTrialEnded(Subscription $subscription): void
    {
        abort_unless($subscription->user && $subscription->trial_ends_at?->isPast(), 404);
    }
}
