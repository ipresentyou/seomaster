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

/**
 * Runs daily.
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
        $expired = Subscription::query()
            ->where('status', 'trial')
            ->where('trial_ends_at', '<=', now())
            ->with('user', 'plan')
            ->get();

        if ($expired->isEmpty()) {
            Log::info('[ExpireTrials] No expired trials found.');
            return;
        }

        Log::info("[ExpireTrials] Processing {$expired->count()} expired trial(s).");

        foreach ($expired as $subscription) {
            try {
                $subscription->update([
                    'status'       => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                if ($subscription->user && $subscription->user->email) {
                    Mail::to($subscription->user->email)
                        ->send(new TrialExpiredMail($subscription));
                }

                Log::info("[ExpireTrials] Expired trial for user #{$subscription->user_id} ({$subscription->user?->email}).");
            } catch (\Throwable $e) {
                Log::error("[ExpireTrials] Failed for subscription #{$subscription->id}: {$e->getMessage()}");
            }
        }

        Log::info("[ExpireTrials] Done. {$expired->count()} trial(s) expired.");
    }
}
