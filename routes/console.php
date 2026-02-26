<?php

use App\Jobs\CleanupOldDataJob;
use App\Jobs\ExpireTrialsJob;
use App\Jobs\SendPaymentFailedReminderJob;
use App\Jobs\SendRenewalReminderJob;
use App\Jobs\SendTrialWarningJob;
use Illuminate\Support\Facades\Schedule;

// =============================================================================
// Lavarell — Task Schedule
// =============================================================================
//
// Setup:
//   1. Add to server crontab (run once):
//        * * * * * cd /var/www/lavarell && php artisan schedule:run >> /dev/null 2>&1
//
//   2. Or use Laravel Scheduler (Horizon / queue worker):
//        php artisan schedule:work  (for local dev)
//
//   3. All jobs are queued — make sure a queue worker is running:
//        php artisan queue:work --queue=default,mail --tries=3
//
// =============================================================================

// ─── 1. Trial Warnings ────────────────────────────────────────────────────────
// Send warnings 3 days and 1 day before trial ends.

Schedule::job(new SendTrialWarningJob(3))
    ->dailyAt('09:00')
    ->name('trial-warning-3d')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] trial-warning-3d failed'));

Schedule::job(new SendTrialWarningJob(1))
    ->dailyAt('09:00')
    ->name('trial-warning-1d')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] trial-warning-1d failed'));

// ─── 2. Expire Trials ────────────────────────────────────────────────────────
// Runs at 00:05 — expire trials that ended yesterday/today and notify users.

Schedule::job(new ExpireTrialsJob())
    ->dailyAt('00:05')
    ->name('expire-trials')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] expire-trials failed'));

// ─── 3. Renewal Reminders ─────────────────────────────────────────────────────
// Remind active subscribers 14 days before annual renewal (e.g. €490 charge upcoming).
// Monthly subscribers also get a 3-day heads up.

Schedule::job(new SendRenewalReminderJob(14))
    ->dailyAt('10:00')
    ->name('renewal-reminder-14d')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] renewal-reminder-14d failed'));

Schedule::job(new SendRenewalReminderJob(3))
    ->dailyAt('10:00')
    ->name('renewal-reminder-3d')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] renewal-reminder-3d failed'));

// ─── 4. Payment Failed Reminders + Grace Period Suspension ────────────────────
// Day 3: follow-up nudge.
// Day 7: final warning + suspend subscription.

Schedule::job(new SendPaymentFailedReminderJob())
    ->dailyAt('11:00')
    ->name('payment-failed-reminders')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] payment-failed-reminders failed'));

// ─── 5. Cleanup ───────────────────────────────────────────────────────────────
// Weekly cleanup: logs >90 days, pending subs, soft-deleted users, orphans.

Schedule::job(new CleanupOldDataJob())
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->name('cleanup-old-data')
    ->withoutOverlapping()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('[Schedule] cleanup-old-data failed'));

// ─── Artisan Helpers ──────────────────────────────────────────────────────────

// Manual trigger commands for admin/ops use:
//
//   php artisan lavarell:expire-trials
//   php artisan lavarell:trial-warnings --days=3
//   php artisan lavarell:cleanup

Artisan::command('lavarell:expire-trials', function () {
    dispatch(new ExpireTrialsJob());
    $this->info('ExpireTrialsJob dispatched.');
})->purpose('Manually expire all overdue trials and notify users');

Artisan::command('lavarell:trial-warnings {--days=3}', function () {
    $days = (int) $this->option('days');
    dispatch(new SendTrialWarningJob($days));
    $this->info("TrialWarningJob ({$days}d) dispatched.");
})->purpose('Send trial-ending warnings. Use --days=1 or --days=3');

Artisan::command('lavarell:renewal-reminders {--days=14}', function () {
    $days = (int) $this->option('days');
    dispatch(new SendRenewalReminderJob($days));
    $this->info("RenewalReminderJob ({$days}d) dispatched.");
})->purpose('Send renewal reminders. Use --days=14 or --days=3');

Artisan::command('lavarell:payment-reminders', function () {
    dispatch(new SendPaymentFailedReminderJob());
    $this->info('PaymentFailedReminderJob dispatched.');
})->purpose('Process failed payment reminders and suspend after grace period');

Artisan::command('lavarell:cleanup', function () {
    dispatch(new CleanupOldDataJob());
    $this->info('CleanupOldDataJob dispatched.');
})->purpose('Run weekly cleanup: logs, pending subs, soft-deleted users');

// ─── Quick status check ───────────────────────────────────────────────────────

Artisan::command('lavarell:status', function () {
    $trials     = \App\Models\Subscription::where('status', 'trial')->count();
    $active     = \App\Models\Subscription::where('status', 'active')->count();
    $cancelled  = \App\Models\Subscription::where('status', 'cancelled')->count();
    $expiring3d = \App\Models\Subscription::where('status', 'trial')
                    ->whereDate('trial_ends_at', now()->addDays(3)->toDateString())
                    ->count();
    $failedInv  = \App\Models\SubscriptionInvoice::where('status', 'failed')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();

    $this->table(
        ['Metric', 'Count'],
        [
            ['Active subscriptions', $active],
            ['Active trials', $trials],
            ['Cancelled', $cancelled],
            ['Trials expiring in 3 days', $expiring3d],
            ['Failed invoices (last 7d)', $failedInv],
        ]
    );
})->purpose('Show a quick subscription & billing status overview');

// ─── Install Wizard ────────────────────────────────────────────────────────────
// The full installer lives at: app/Console/Commands/InstallCommand.php
// Laravel 11 auto-discovers it — no manual registration needed.
//
// Run:
//   php artisan lavarell:install
//   php artisan lavarell:install --force
//   php artisan lavarell:install --skip-migrations --admin-email=me@domain.de
