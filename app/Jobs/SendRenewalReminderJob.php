<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\RenewalReminderMail;
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
 * Sends a renewal reminder to active (non-trial) subscribers
 * whose current_period_end is exactly $daysAhead days away.
 *
 * Useful for yearly plans — reminds users 14 days before they're charged.
 */
class SendRenewalReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        private readonly int $daysAhead = 14
    ) {}

    public function handle(): void
    {
        $targetDate = now()->addDays($this->daysAhead);

        $subscriptions = Subscription::query()
            ->where('status', 'active')
            ->whereDate('current_period_end', $targetDate->toDateString())
            ->with('user', 'plan')
            ->get();

        if ($subscriptions->isEmpty()) {
            Log::info("[RenewalReminder] No renewals in {$this->daysAhead} day(s).");
            return;
        }

        Log::info("[RenewalReminder] Sending {$this->daysAhead}-day reminders to {$subscriptions->count()} subscriber(s).");

        foreach ($subscriptions as $subscription) {
            try {
                if (! $subscription->user?->email) {
                    continue;
                }

                Mail::to($subscription->user->email)
                    ->send(new RenewalReminderMail($subscription, $this->daysAhead));

                Log::info("[RenewalReminder] Sent to {$subscription->user->email} ({$subscription->billing_cycle}).");
            } catch (\Throwable $e) {
                Log::error("[RenewalReminder] Failed for subscription #{$subscription->id}: {$e->getMessage()}");
            }
        }
    }
}
