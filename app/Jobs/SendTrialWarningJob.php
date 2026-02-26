<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\TrialEndingMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Runs daily.
 * Sends trial-ending warnings when trial_ends_at is exactly N days away.
 * Dispatched with a $daysAhead parameter (e.g. 3 or 1).
 */
class SendTrialWarningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        private readonly int $daysAhead = 3
    ) {}

    public function handle(): void
    {
        // Match subscriptions expiring on exactly `$daysAhead` days from now (whole day window)
        $targetDate = now()->addDays($this->daysAhead);

        $subscriptions = Subscription::query()
            ->where('status', 'trial')
            ->whereDate('trial_ends_at', $targetDate->toDateString())
            ->with('user', 'plan')
            ->get();

        if ($subscriptions->isEmpty()) {
            Log::info("[TrialWarning] No trials expiring in {$this->daysAhead} day(s).");
            return;
        }

        Log::info("[TrialWarning] Sending {$this->daysAhead}-day warnings to {$subscriptions->count()} user(s).");

        foreach ($subscriptions as $subscription) {
            try {
                if (! $subscription->user?->email) {
                    continue;
                }

                Mail::to($subscription->user->email)
                    ->send(new TrialEndingMail($subscription, $this->daysAhead));

                Log::info("[TrialWarning] Sent {$this->daysAhead}d warning to {$subscription->user->email}.");
            } catch (\Throwable $e) {
                Log::error("[TrialWarning] Failed for subscription #{$subscription->id}: {$e->getMessage()}");
            }
        }
    }
}
