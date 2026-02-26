<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\PaymentFailedMail;
use App\Models\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Runs daily.
 * Finds invoices with status=failed that are between 1–7 days old
 * and sends a follow-up payment reminder.
 *
 * Avoids duplicate sends by only targeting specific age windows:
 *   - Day 1: first reminder (sent by webhook immediately)
 *   - Day 3: follow-up reminder
 *   - Day 7: final reminder (after this, subscription may be suspended)
 */
class SendPaymentFailedReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    /** Which days after failure to send reminders */
    private const REMINDER_DAYS = [3, 7];

    public function handle(): void
    {
        foreach (self::REMINDER_DAYS as $daysAgo) {
            $this->sendRemindersForDay($daysAgo);
        }

        // After 7 days of failed payment → suspend subscription
        $this->suspendAfterGracePeriod();
    }

    private function sendRemindersForDay(int $daysAgo): void
    {
        $targetDate = now()->subDays($daysAgo);

        $invoices = SubscriptionInvoice::query()
            ->where('status', 'failed')
            ->whereDate('created_at', $targetDate->toDateString())
            ->with('subscription.user', 'subscription.plan')
            ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        Log::info("[PaymentFailed] Day-{$daysAgo} reminders: {$invoices->count()} invoice(s).");

        foreach ($invoices as $invoice) {
            $user = $invoice->subscription?->user;

            if (! $user?->email) {
                continue;
            }

            try {
                Mail::to($user->email)
                    ->send(new PaymentFailedMail($invoice, $daysAgo));

                Log::info("[PaymentFailed] Day-{$daysAgo} reminder sent to {$user->email}.");
            } catch (\Throwable $e) {
                Log::error("[PaymentFailed] Failed for invoice #{$invoice->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * After 7-day grace period, suspend the subscription.
     */
    private function suspendAfterGracePeriod(): void
    {
        $gracePeriodEnd = now()->subDays(7);

        $invoices = SubscriptionInvoice::query()
            ->where('status', 'failed')
            ->where('created_at', '<=', $gracePeriodEnd)
            ->whereHas('subscription', fn($q) => $q->where('status', 'active'))
            ->with('subscription')
            ->get();

        foreach ($invoices as $invoice) {
            try {
                $invoice->subscription?->update([
                    'status' => 'suspended',
                ]);

                Log::warning(
                    "[PaymentFailed] Suspended subscription #{$invoice->subscription_id} after 7-day grace period."
                );
            } catch (\Throwable $e) {
                Log::error("[PaymentFailed] Suspend failed for invoice #{$invoice->id}: {$e->getMessage()}");
            }
        }
    }
}
