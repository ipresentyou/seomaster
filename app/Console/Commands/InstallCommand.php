<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Interactive SEOmaster installer.
 *
 * Usage:
 *   php artisan lavarell:install
 *
 * What it does:
 *   1.  System requirements check  (PHP version, required extensions)
 *   2.  .env setup                 (copy from .env.example if missing, edit key values)
 *   3.  App key generation         (if APP_KEY is empty)
 *   4.  Database connection test
 *   5.  Run migrations
 *   6.  Run seeders                (Roles/Permissions + Subscription Plans)
 *   7.  Create admin user          (interactive, with validation)
 *   8.  Storage symlink
 *   9.  Queue / cache driver hints
 *   10. Crontab instruction
 *   11. Final summary
 */
class InstallCommand extends Command
{
    protected $signature = 'lavarell:install
                            {--force : Skip all confirmation prompts}
                            {--skip-migrations : Do not run migrations (use if already done)}
                            {--skip-seeders : Do not run seeders}
                            {--admin-email= : Admin e-mail (non-interactive)}
                            {--admin-name= : Admin name (non-interactive)}
                            {--admin-password= : Admin password (non-interactive)}';

    protected $description = 'Interactive SEOmaster setup wizard — runs migrations, seeders, creates admin user';

    // ── Step counter ──────────────────────────────────────────────────────────

    private int $step    = 0;
    private int $total   = 10;
    private int $errors  = 0;
    private int $warnings = 0;

    // ─────────────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $this->banner();

        // ── Steps ─────────────────────────────────────────────────────────────
        $this->checkRequirements();      // 1
        $this->setupEnv();               // 2
        $this->generateAppKey();         // 3
        $this->testDatabase();           // 4 — aborts on failure
        $this->runMigrations();          // 5
        $this->runSeeders();             // 6
        $this->createAdminUser();        // 7
        $this->createStorageLink();      // 8
        $this->printQueueHints();        // 9
        $this->printCrontabInstruction();// 10
        $this->printSummary();           // Final

        return $this->errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    // =========================================================================
    // STEP 1 — Requirements
    // =========================================================================

    private function checkRequirements(): void
    {
        $this->stepHeader(1, 'Systemvoraussetzungen prüfen');

        // PHP version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.2.0', '>=')) {
            $this->okStep("PHP {$phpVersion}");
        } else {
            $this->failStep("PHP {$phpVersion} — mindestens PHP 8.2 erforderlich");
        }

        // Required extensions
        $required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'curl', 'bcmath'];
        foreach ($required as $ext) {
            if (extension_loaded($ext)) {
                $this->okStep("ext-{$ext}");
            } else {
                $this->failStep("ext-{$ext} fehlt");
            }
        }

        // Optional but recommended
        $optional = ['redis' => 'Queue/Cache empfohlen', 'gd' => 'Bildverarbeitung', 'zip' => 'Package-Management'];
        foreach ($optional as $ext => $hint) {
            if (extension_loaded($ext)) {
                $this->okStep("ext-{$ext} (optional)");
            } else {
                $this->warnStep("ext-{$ext} nicht geladen — {$hint}");
            }
        }

        // Writable directories
        $dirs = ['storage', 'storage/logs', 'storage/framework', 'bootstrap/cache'];
        foreach ($dirs as $dir) {
            $path = base_path($dir);
            if (is_writable($path)) {
                $this->okStep("{$dir} ist beschreibbar");
            } else {
                $this->failStep("{$dir} ist NICHT beschreibbar — chmod 775 {$dir}");
            }
        }
    }

    // =========================================================================
    // STEP 2 — .env setup
    // =========================================================================

    private function setupEnv(): void
    {
        $this->stepHeader(2, '.env konfigurieren');

        $envPath     = base_path('.env');
        $examplePath = base_path('.env.example');

        // Copy .env.example → .env if missing
        if (! File::exists($envPath)) {
            if (File::exists($examplePath)) {
                File::copy($examplePath, $envPath);
                $this->okStep('.env aus .env.example erstellt');
            } else {
                $this->failStep('.env.example nicht gefunden — kann .env nicht erstellen');
                return;
            }
        } else {
            $this->okStep('.env existiert bereits');
        }

        // Interactive ENV configuration
        if (! $this->option('force') && $this->confirm('Wichtige .env-Werte jetzt konfigurieren?', true)) {
            $this->configureEnvValue('APP_URL', 'App-URL (z.B. https://lavarell.de)', env('APP_URL', 'http://localhost'));
            $this->configureEnvValue('APP_NAME', 'App-Name', env('APP_NAME', 'SEOmaster'));

            $this->line('');
            $this->comment('  ── Datenbank ────────────────────────────────────');
            $this->configureEnvValue('DB_HOST',     'DB Host',     env('DB_HOST', '127.0.0.1'));
            $this->configureEnvValue('DB_PORT',     'DB Port',     env('DB_PORT', '3306'));
            $this->configureEnvValue('DB_DATABASE', 'DB Name',     env('DB_DATABASE', 'lavarell'));
            $this->configureEnvValue('DB_USERNAME', 'DB Username', env('DB_USERNAME', 'root'));
            $dbPass = $this->secret('  DB_PASSWORD [leer lassen wenn keins]:');
            if ($dbPass !== null) {
                $this->setEnvValue('DB_PASSWORD', $dbPass);
            }

            $this->line('');
            $this->comment('  ── Mail ─────────────────────────────────────────');
            $this->configureEnvValue('MAIL_FROM_ADDRESS', 'Absender-E-Mail', env('MAIL_FROM_ADDRESS', 'noreply@lavarell.de'));
            $this->configureEnvValue('MAIL_FROM_NAME',    'Absender-Name',   env('MAIL_FROM_NAME', 'SEOmaster'));

            $this->line('');
            $this->comment('  ── Queue ────────────────────────────────────────');
            $queueDriver = $this->choice(
                '  Queue-Treiber',
                ['redis', 'database', 'sync'],
                0
            );
            $this->setEnvValue('QUEUE_CONNECTION', $queueDriver);
            $this->okStep("QUEUE_CONNECTION={$queueDriver}");

            $this->line('');
            $this->okStep('.env konfiguriert');
        } else {
            $this->info('  ℹ  .env nicht verändert — werte manuell nach dem Setup an');
        }
    }

    // =========================================================================
    // STEP 3 — App Key
    // =========================================================================

    private function generateAppKey(): void
    {
        $this->stepHeader(3, 'App-Key generieren');

        $key = env('APP_KEY', '');

        if (! empty($key) && str_starts_with($key, 'base64:')) {
            $this->okStep('APP_KEY ist bereits gesetzt');
            return;
        }

        Artisan::call('key:generate', ['--force' => true]);
        $this->okStep('APP_KEY generiert und in .env geschrieben');
    }

    // =========================================================================
    // STEP 4 — Database connection test
    // =========================================================================

    private function testDatabase(): void
    {
        $this->stepHeader(4, 'Datenbankverbindung testen');

        try {
            DB::connection()->getPdo();
            $driver  = DB::connection()->getDriverName();
            $dbName  = DB::connection()->getDatabaseName();
            $this->okStep("Verbindung OK — {$driver}:{$dbName}");
        } catch (Throwable $e) {
            $this->failStep('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
            $this->line('');
            $this->error('  Installation abgebrochen. Bitte .env-Datenbankwerte prüfen.');
            exit(self::FAILURE);
        }
    }

    // =========================================================================
    // STEP 5 — Migrations
    // =========================================================================

    private function runMigrations(): void
    {
        $this->stepHeader(5, 'Migrationen ausführen');

        if ($this->option('skip-migrations')) {
            $this->warnStep('  Übersprungen (--skip-migrations)');
            return;
        }

        // Check if already migrated
        $alreadyMigrated = false;
        try {
            $alreadyMigrated = DB::table('migrations')->exists();
        } catch (Throwable) {}

        if ($alreadyMigrated && ! $this->option('force')) {
            if (! $this->confirm('  Datenbank enthält bereits Migrationen. Fortfahren mit migrate?', false)) {
                $this->warnStep('  Migrationen übersprungen');
                return;
            }
        }

        $this->line('  Führe php artisan migrate aus...');

        try {
            Artisan::call('migrate', ['--force' => true], $this->output);
            $this->okStep('Migrationen abgeschlossen');
        } catch (Throwable $e) {
            $this->failStep('Migration fehlgeschlagen: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // STEP 6 — Seeders
    // =========================================================================

    private function runSeeders(): void
    {
        $this->stepHeader(6, 'Daten seeden (Rollen, Pläne)');

        if ($this->option('skip-seeders')) {
            $this->warnStep('  Übersprungen (--skip-seeders)');
            return;
        }

        // Roles & Permissions
        $this->line('  Seeding Rollen & Permissions...');
        try {
            Artisan::call('db:seed', [
                '--class' => RolesAndPermissionsSeeder::class,
                '--force' => true,
            ]);
            $this->okStep('Rollen, Permissions und Admin-User angelegt');
        } catch (Throwable $e) {
            $this->failStep('RolesAndPermissionsSeeder: ' . $e->getMessage());
        }

        // Subscription Plans
        $this->line('  Seeding Subscription Plans...');
        try {
            Artisan::call('db:seed', [
                '--class' => SubscriptionPlanSeeder::class,
                '--force' => true,
            ]);
            $this->okStep('Starter / Pro / Agency Pläne angelegt');
        } catch (Throwable $e) {
            $this->failStep('SubscriptionPlanSeeder: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // STEP 7 — Admin user
    // =========================================================================

    private function createAdminUser(): void
    {
        $this->stepHeader(7, 'Admin-User erstellen / prüfen');

        // Check if admin already exists from seeder
        $existingAdmin = User::where('email', 'admin@lavarell.com')->first();
        if ($existingAdmin) {
            $this->okStep('Standard-Admin admin@lavarell.com existiert bereits');
            $this->line('');
            $this->warnStep('  ⚠  Standard-Passwort ist "changeme123!" — bitte sofort ändern!');
            $this->comment('     php artisan lavarell:install --admin-email=... oder im Admin-Panel');
        }

        // Ask to create additional admin
        $createNew = $this->option('admin-email')
            || ($this->option('force')
                ? false
                : $this->confirm('  Neuen eigenen Admin-User anlegen?', true));

        if (! $createNew && ! $this->option('admin-email')) {
            return;
        }

        // Collect credentials
        $email    = $this->option('admin-email')    ?: $this->askWithValidation('  Admin E-Mail',    'email');
        $name     = $this->option('admin-name')     ?: $this->ask('  Admin Name', 'Admin');
        $password = $this->option('admin-password') ?: $this->askPassword();

        // Check for existing user
        if (User::where('email', $email)->exists()) {
            $this->warnStep("  User {$email} existiert bereits — übersprungen");
            return;
        }

        try {
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'status'            => 'active',
            ]);

            $user->assignRole('admin');

            $this->okStep("Admin-User erstellt: {$email}");
            $this->line('');
            $this->info("  📧 E-Mail:     {$email}");
            $this->info("  👤 Name:       {$name}");
            $this->info("  🔑 Passwort:   [wie eingegeben]");
            $this->info("  🌐 Admin-URL:  " . config('app.url') . '/admin');
        } catch (Throwable $e) {
            $this->failStep('Admin-User konnte nicht angelegt werden: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // STEP 8 — Storage symlink
    // =========================================================================

    private function createStorageLink(): void
    {
        $this->stepHeader(8, 'Storage-Symlink erstellen');

        $publicStorage = public_path('storage');

        if (file_exists($publicStorage)) {
            $this->okStep('public/storage Symlink existiert bereits');
            return;
        }

        try {
            Artisan::call('storage:link');
            $this->okStep('public/storage → storage/app/public Symlink erstellt');
        } catch (Throwable $e) {
            $this->warnStep('Symlink konnte nicht erstellt werden: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // STEP 9 — Queue / Cache hints
    // =========================================================================

    private function printQueueHints(): void
    {
        $this->stepHeader(9, 'Queue & Cache');

        $queue = env('QUEUE_CONNECTION', 'sync');
        $cache = env('CACHE_STORE', 'file');

        if ($queue === 'sync') {
            $this->warnStep('QUEUE_CONNECTION=sync — E-Mails und Jobs werden synchron ausgeführt');
            $this->warnStep('Für Produktion: redis oder database empfohlen');
        } else {
            $this->okStep("QUEUE_CONNECTION={$queue}");
            $this->line('');
            $this->comment('  Queue-Worker starten (Supervisor empfohlen):');
            $this->line('  <fg=cyan>php artisan queue:work --queue=default,mail --tries=3 --sleep=3</>');
        }

        $this->line('');

        if (in_array($cache, ['redis', 'memcached'])) {
            $this->okStep("CACHE_STORE={$cache}");
        } else {
            $this->warnStep("CACHE_STORE={$cache} — Redis für Produktion empfohlen");
        }

        $this->line('');
        $this->comment('  Cache leeren:');
        $this->line('  <fg=cyan>php artisan config:cache && php artisan route:cache && php artisan view:cache</>');
    }

    // =========================================================================
    // STEP 10 — Crontab
    // =========================================================================

    private function printCrontabInstruction(): void
    {
        $this->stepHeader(10, 'Crontab einrichten');

        $appPath = base_path();

        $this->line('  Füge folgende Zeile in deinen Crontab ein (<fg=cyan>crontab -e</>):');
        $this->line('');
        $this->line("  <fg=green>* * * * * cd {$appPath} && php artisan schedule:run >> /dev/null 2>&1</>");
        $this->line('');
        $this->comment('  Der Scheduler steuert:');
        $this->line('  <fg=cyan>• Trial-Warnungen (3d + 1d vor Ablauf)</>');
        $this->line('  <fg=cyan>• Trial-Ablauf (tägl. 00:05)</>');
        $this->line('  <fg=cyan>• Verlängerungs-Erinnerungen (14d + 3d)</>');
        $this->line('  <fg=cyan>• Zahlungs-Mahnungen + Suspension nach 7 Tagen</>');
        $this->line('  <fg=cyan>• Wöchentlicher Cleanup (So. 03:00)</>');
        $this->line('');
        $this->comment('  Scheduler lokal testen:');
        $this->line('  <fg=cyan>php artisan schedule:list</>');
        $this->line('  <fg=cyan>php artisan schedule:work</>');
    }

    // =========================================================================
    // FINAL SUMMARY
    // =========================================================================

    private function printSummary(): void
    {
        $this->line('');
        $this->line('  <bg=blue;fg=white>                                                      </> ');
        $this->line('  <bg=blue;fg=white>   ⚡  SEOmaster — Installation abgeschlossen          </> ');
        $this->line('  <bg=blue;fg=white>                                                      </> ');
        $this->line('');

        if ($this->errors > 0) {
            $this->error("  {$this->errors} Fehler aufgetreten — bitte Ausgabe oben prüfen");
        }
        if ($this->warnings > 0) {
            $this->warnStep("  {$this->warnings} Hinweis(e) — optional, aber empfohlen");
        }

        $this->line('');
        $this->info('  🌐 App-URL:      ' . config('app.url'));
        $this->info('  🛡  Admin-Panel:  ' . config('app.url') . '/admin');
        $this->info('  📋 Docs:         https://github.com/lavarell/docs');
        $this->line('');

        $this->comment('  Nächste Schritte:');
        $this->line('  1. PayPal Billing Plans anlegen → Plan-IDs in .env eintragen');
        $this->line('  2. MAIL_* Werte in .env prüfen (Mailgun / SMTP)');
        $this->line('  3. Admin-Passwort ändern: ' . config('app.url') . '/admin');
        $this->line('  4. Crontab einrichten (Schritt 10 oben)');
        $this->line('  5. Queue-Worker starten (für E-Mails + Jobs)');
        $this->line('');

        $this->comment('  Nützliche Befehle:');
        $this->line('  <fg=cyan>php artisan lavarell:status</>             — Abo-Übersicht');
        $this->line('  <fg=cyan>php artisan lavarell:expire-trials</>      — Trials sofort prüfen');
        $this->line('  <fg=cyan>php artisan queue:work</>                  — Queue-Worker starten');
        $this->line('  <fg=cyan>php artisan schedule:list</>               — Schedule-Übersicht');
        $this->line('');

        if ($this->errors === 0) {
            $this->line('  <fg=green>✅ Viel Erfolg mit SEOmaster!</>');
        }

        $this->line('');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function banner(): void
    {
        $this->line('');
        $this->line('  <fg=magenta>  ██╗      █████╗ ██╗   ██╗ █████╗ ██████╗ ███████╗██╗     ██╗</>');
        $this->line('  <fg=magenta>  ██║     ██╔══██╗██║   ██║██╔══██╗██╔══██╗██╔════╝██║     ██║</>');
        $this->line('  <fg=magenta>  ██║     ███████║██║   ██║███████║██████╔╝█████╗  ██║     ██║</>');
        $this->line('  <fg=magenta>  ██║     ██╔══██║╚██╗ ██╔╝██╔══██║██╔══██╗██╔══╝  ██║     ██║</>');
        $this->line('  <fg=magenta>  ███████╗██║  ██║ ╚████╔╝ ██║  ██║██║  ██║███████╗███████╗███████╗</>');
        $this->line('  <fg=magenta>  ╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝</>');
        $this->line('');
        $this->line('  <fg=white;options=bold>  SEO Automation für Shopware  ·  Setup-Wizard v1.0</>');
        $this->line('  <fg=gray>  Laravel ' . app()->version() . '  ·  PHP ' . PHP_VERSION . '</>');
        $this->line('');
        $this->line('  ──────────────────────────────────────────────────────────');
        $this->line('');
    }

    private function stepHeader(int $n, string $title): void
    {
        $this->line('');
        $this->line("  <fg=yellow;options=bold>  [{$n}/{$this->total}]</> <fg=white;options=bold>{$title}</>");
        $this->line('  ' . str_repeat('─', 54));
    }

    public function okStep(string $message): void
    {
        $this->line("  <fg=green>  ✓</> {$message}");
    }

    public function warnStep(string $message): void
    {
        $this->warnings++;
        $this->line("  <fg=yellow>  ⚠</> {$message}");
    }

    public function failStep(string $message): void
    {
        $this->errors++;
        $this->line("  <fg=red>  ✗</> {$message}");
    }

    /**
     * Ask a question and write the answer back to .env.
     */
    private function configureEnvValue(string $key, string $label, string $default = ''): void
    {
        $current = env($key, $default);
        $value   = $this->ask("  {$label}", $current);

        if ($value !== $current) {
            $this->setEnvValue($key, $value);
        }

        $this->okStep("{$key}={$value}");
    }

    /**
     * Write or update a single key=value in .env.
     */
    private function setEnvValue(string $key, string $value): bool
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            return false;
        }

        $content = File::get($envPath);

        // Wrap value in quotes if it contains spaces
        $escaped = str_contains($value, ' ') ? "\"{$value}\"" : $value;

        if (str_contains($content, "{$key}=")) {
            // Replace existing
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$escaped}",
                $content
            );
        } else {
            // Append
            $content .= PHP_EOL . "{$key}={$escaped}";
        }

        File::put($envPath, $content);

        return true;
    }

    /**
     * Ask with Validator-based validation.
     */
    private function askWithValidation(string $question, string $rule): string
    {
        while (true) {
            $value = $this->ask($question);
            $validator = Validator::make([$rule => $value], [$rule => [$rule]]);

            if ($validator->passes()) {
                return $value;
            }

            $this->error('  Ungültige Eingabe: ' . $validator->errors()->first());
        }
    }

    /**
     * Ask for password with minimum length check and confirmation.
     */
    private function askPassword(): string
    {
        while (true) {
            $password = $this->secret('  Passwort (min. 8 Zeichen)');

            if (strlen($password) < 8) {
                $this->error('  Passwort muss mindestens 8 Zeichen lang sein.');
                continue;
            }

            $confirm = $this->secret('  Passwort bestätigen');

            if ($password !== $confirm) {
                $this->error('  Passwörter stimmen nicht überein.');
                continue;
            }

            return $password;
        }
    }
}
