<?php

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class RegistrationSpamTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-22 10:00:00');
        foreach ([
            '2024_01_01_000001_create_users_table.php',
            '2024_01_01_000003_create_subscriptions_table.php',
            '2024_01_01_000005_add_onboarding_to_users_table.php',
            '2026_05_29_000002_add_billing_fields_to_users_table.php',
            '2026_10_31_000001_allow_pending_subscriptions.php',
        ] as $migration) {
            (require database_path('migrations/' . $migration))->up();
        }
        SubscriptionPlan::firstOrCreate(['slug' => 'starter'], ['name' => 'Starter', 'price_monthly' => 19]);
        Mail::fake();
        Notification::fake();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function register(array $overrides = [], int $secondsAfterForm = 10)
    {
        $this->get('/register')->assertOk();
        Carbon::setTestNow(now()->addSeconds($secondsAfterForm));

        return $this->post('/register', array_merge([
            'name' => 'Anna-Lena Müller', 'email' => 'anna@example.test',
            'password' => 'Sicheres!Passwort2026', 'password_confirmation' => 'Sicheres!Passwort2026',
            'terms' => '1', 'website' => '',
        ], $overrides));
    }

    public function test_real_person_can_register(): void
    {
        $this->register()->assertRedirect(route('verification.notice'));
        $this->assertSame(1, User::count());
    }

    public function test_names_with_apostrophe_and_title_are_allowed(): void
    {
        $this->register(['name' => "Dr. Renée O'Connor"])->assertRedirect(route('verification.notice'));
        $this->assertSame(1, User::count());
    }

    /** @dataProvider spamNames */
    public function test_names_with_links_or_digits_are_rejected(string $name): void
    {
        $this->register(['name' => $name])->assertSessionHasErrors('name');
        $this->assertSame(0, User::count());
    }

    public static function spamNames(): array
    {
        return [
            'gesehener Spam' => ['Zorblax7412 forms.gle/Miu8nxZdPgUwPGb16'],
            'domain'         => ['Gewinn auf example.com'],
            'url'            => ['https://spam.test'],
            'www'            => ['www.spam.test'],
            'ziffern'        => ['Max 1234'],
        ];
    }

    public function test_filled_honeypot_is_rejected(): void
    {
        $this->register(['website' => 'http://spam.test'])->assertSessionHasErrors('email');
        $this->assertSame(0, User::count());
    }

    public function test_too_fast_submission_is_rejected(): void
    {
        $this->register([], 1)->assertSessionHasErrors('email');
        $this->assertSame(0, User::count());
    }

    public function test_post_without_loading_form_is_rejected(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/register', [
                'name' => 'Anna Müller', 'email' => 'anna@example.test',
                'password' => 'Sicheres!Passwort2026', 'password_confirmation' => 'Sicheres!Passwort2026',
                'terms' => '1',
            ])->assertSessionHasErrors('email');
        $this->assertSame(0, User::count());
    }
}
