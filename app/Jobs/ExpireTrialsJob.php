<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\TrialExpiredMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

/**
 * Runs hourly via deployment/seomaster-trials.cron (also safe to run manually).
 * Finds all trial subscriptions whose trial_ends_at has passed,
 * marks them as cancelled, and notifies the user.
 */
class ExpireTrialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function handle(): void
    {
        // Access checks enforce the deadline independently of this status reconciliation.
        Subscription::query()
            ->where('status', 'trial')
            ->where('trial_ends_at', '<=', now())
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        // Retry pending deliveries, without mailing months-old trials at rollout.
        $candidates = Subscription::where('status', 'cancelled')
            ->whereBetween('trial_ends_at', [now()->subDays(7), now()])
            ->whereNull('trial_feedback_sent_at')
            ->whereHas('user', fn ($user) => $user->whereNotNull('email_verified_at'))
            ->pluck('id');

        $failed = false;
        foreach ($candidates as $id) {
            try {
                DB::transaction(function () use ($id) {
                    $subscription = Subscription::with('user', 'plan')->lockForUpdate()->find($id);
                    if (! $subscription || $subscription->trial_feedback_sent_at || $subscription->status !== 'cancelled') {
                        return;
                    }
                    if (! $subscription->user || $subscription->user->hasActiveSubscription()) {
                        return;
                    }
                    Mail::to($subscription->user->email)->send(new TrialExpiredMail($subscription));
                    $subscription->update(['trial_feedback_sent_at' => now()]);
                });
            } catch (\Throwable $e) {
                $failed = true;
                Log::error('[ExpireTrials] Feedback delivery failed', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            }
        }

        if ($failed) {
            throw new \RuntimeException('Trial feedback delivery failed; pending deliveries will be retried.');
        }
    }
}
