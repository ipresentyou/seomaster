# Trial lifecycle

Install `seomaster-trials.cron` as `/etc/cron.d/seomaster-trials`, owned by root with mode 0644. The cron service dispatches the trial lifecycle hourly at minute 5 as www-data; the existing Redis queue worker processes it. Restart queue workers after deploying changes.

Only the trial lifecycle is activated by this file, not the unrelated billing schedules in `routes/console.php`. If the full Laravel scheduler is enabled later, remove this dedicated cron entry.

Deploy migration `2026_10_30_000001_add_trial_feedback.php` explicitly. The local, unfinished TYPO3 migration must not be deployed.

Access expires at `trial_ends_at`, regardless of the stored trial status or scheduler availability. The job changes expired trial statuses to cancelled. Verified accounts whose trial ended within the past seven days receive the expiry/feedback mail; converted accounts and old trials are skipped. A delivery timestamp and row lock prevent repeat sends in normal processing. Failed sends remain pending and retry through the queue and the next hourly run. SMTP acknowledgement and the database commit are not atomic, so an ambiguous transport failure may still cause a duplicate delivery.

Feedback links expire after 30 days and work without login. Responses are stored once per subscription and appear under **Admin → Testphasen-Feedback**. No historical bulk mailing is performed.

Run `vendor/bin/phpunit` for the SQLite in-memory regression tests. No customer database or real mail delivery is used by those tests.
