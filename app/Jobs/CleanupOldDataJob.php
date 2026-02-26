<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\SeoActivityLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Runs weekly (Sunday 03:00).
 * Cleans up database clutter to keep the platform fast:
 *
 *   1. Activity logs older than 90 days
 *   2. Pending subscriptions older than 24 hours (abandoned checkouts)
 *   3. Soft-deleted users inactive for 60+ days (GDPR-style purge)
 *   4. Orphaned cancelled subscriptions with no user (edge cases)
 */
class CleanupOldDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;

    public function handle(): void
    {
        $report = [];

        // 1. Old activity logs
        $report['activity_logs'] = $this->cleanActivityLogs();

        // 2. Abandoned pending subscriptions
        $report['pending_subs'] = $this->cleanPendingSubscriptions();

        // 3. Soft-deleted users (GDPR)
        $report['deleted_users'] = $this->purgeDeletedUsers();

        // 4. Orphaned subscriptions
        $report['orphaned_subs'] = $this->cleanOrphanedSubscriptions();

        $total = array_sum($report);
        Log::info('[Cleanup] Done.', array_merge($report, ['total_deleted' => $total]));
    }

    // ── Cleanup Methods ───────────────────────────────────────────────────────

    private function cleanActivityLogs(): int
    {
        $count = SeoActivityLog::query()
            ->where('created_at', '<', now()->subDays(90))
            ->count();

        SeoActivityLog::query()
            ->where('created_at', '<', now()->subDays(90))
            ->delete();

        if ($count > 0) {
            Log::info("[Cleanup] Deleted {$count} activity log(s) older than 90 days.");
        }

        return $count;
    }

    private function cleanPendingSubscriptions(): int
    {
        $count = Subscription::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->count();

        Subscription::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        if ($count > 0) {
            Log::info("[Cleanup] Deleted {$count} abandoned pending subscription(s).");
        }

        return $count;
    }

    private function purgeDeletedUsers(): int
    {
        // Only purge users soft-deleted 60+ days ago
        $count = User::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(60))
            ->count();

        User::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(60))
            ->forceDelete();

        if ($count > 0) {
            Log::info("[Cleanup] Force-deleted {$count} user(s) (GDPR, 60+ days).");
        }

        return $count;
    }

    private function cleanOrphanedSubscriptions(): int
    {
        // Subscriptions whose user_id no longer exists (no soft-delete reference)
        $count = DB::table('subscriptions')
            ->leftJoin('users', 'subscriptions.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();

        DB::table('subscriptions')
            ->leftJoin('users', 'subscriptions.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->delete();

        if ($count > 0) {
            Log::warning("[Cleanup] Deleted {$count} orphaned subscription(s) (no user found).");
        }

        return $count;
    }
}
