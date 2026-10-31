<?php

use App\Http\Middleware\EnsureSubscriptionIsActive;
use App\Jobs\ExpireTrialsJob;
use App\Mail\TrialExpiredMail;
use App\Mail\TrialFeedbackReceived;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\TrialFeedback;
use App\Models\User;
use App\Services\PayPalService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class TrialLifecycleTest extends TestCase
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
        Carbon::setTestNow('2026-10-30 10:00:00');
        foreach ([
            '2024_01_01_000001_create_users_table.php',
            '2024_01_01_000002_create_api_credentials_table.php',
            '2024_01_01_000003_create_subscriptions_table.php',
            '2024_01_01_000004_create_seo_tables.php',
            '2024_01_01_000005_add_onboarding_to_users_table.php',
            '2026_02_25_210346_create_permission_tables.php',
            '2026_10_30_000001_add_trial_feedback.php',
            '2026_10_31_000001_allow_pending_subscriptions.php',
        ] as $migration) {
            (require database_path('migrations/' . $migration))->up();
        }
        $this->app->instance(PayPalService::class, Mockery::mock(PayPalService::class));
        Mail::fake();
        Route::get('/test-subscription-gate', fn () => 'allowed')
            ->middleware(['web', 'auth', EnsureSubscriptionIsActive::class]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function trial(?string $end = '-1 minute', string $status = 'trial', bool $verified = true): Subscription
    {
        $user = User::forceCreate([
            'name' => 'Trial Tester', 'email' => uniqid() . '@example.test', 'password' => 'unused',
            'status' => 'active', 'email_verified_at' => $verified ? now() : null,
            'onboarding_completed_at' => now(),
        ]);
        $plan = SubscriptionPlan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'price_monthly' => 19,
        ]);
        return Subscription::create([
            'user_id' => $user->id, 'subscription_plan_id' => $plan->id,
            'status' => $status, 'trial_ends_at' => $end === null ? null : now()->modify($end),
        ]);
    }

    public function test_expired_trial_is_blocked_before_cleanup_has_run(): void
    {
        $sub = $this->trial();
        $this->assertSame('trial', $sub->status);
        $this->assertFalse($sub->isActive());
        $this->assertNull($sub->user->activeSubscription);
        $this->actingAs($sub->user)->get('/test-subscription-gate')->assertRedirect(route('subscription.index'));
    }

    public function test_future_trial_is_allowed_but_exact_deadline_and_missing_deadline_are_blocked(): void
    {
        $sub = $this->trial('+1 second');
        $this->actingAs($sub->user)->get('/test-subscription-gate')->assertOk()->assertSee('allowed');
        $this->travel(1)->seconds();
        $this->assertFalse($sub->isActive());
        $this->assertFalse($sub->user->hasActiveSubscription());
        $this->actingAs($sub->user)->get('/test-subscription-gate')->assertRedirect(route('subscription.index'));
        $missing = $this->trial(null);
        $this->assertFalse($missing->isActive());
        $this->assertNull($missing->user->activeSubscription);
    }

    public function test_paid_access_remains_available_even_with_old_trial_date(): void
    {
        $sub = $this->trial('-1 month', 'active');
        $this->assertTrue($sub->isActive());
        $this->actingAs($sub->user)->get('/test-subscription-gate')->assertOk();
    }

    public function test_expired_or_cancelled_trial_cannot_be_restarted(): void
    {
        foreach (['trial', 'cancelled'] as $status) {
            $sub = $this->trial('-1 day', $status);
            $this->actingAs($sub->user)->get(route('subscription.start-trial'))->assertRedirect(route('subscription.index'));
            $this->assertSame(1, $sub->user->subscriptions()->count());
        }
    }

    public function test_job_sends_recent_verified_trials_once_and_skips_old_unverified_and_paid(): void
    {
        $recent = $this->trial('-1 hour');
        $old = $this->trial('-1 month');
        $unverified = $this->trial('-1 hour', 'trial', false);
        $paid = $this->trial('-1 hour', 'active');
        (new ExpireTrialsJob())->handle();
        (new ExpireTrialsJob())->handle();
        Mail::assertSent(TrialExpiredMail::class, 1);
        Mail::assertSent(TrialExpiredMail::class, fn ($mail) => $mail->hasTo($recent->user->email));
        $this->assertNotNull($recent->fresh()->trial_feedback_sent_at);
        $this->assertSame('cancelled', $old->fresh()->status);
        $this->assertNull($old->fresh()->trial_feedback_sent_at);
        $this->assertNull($unverified->fresh()->trial_feedback_sent_at);
        $this->assertSame('active', $paid->fresh()->status);
    }

    public function test_failed_delivery_remains_retryable(): void
    {
        $sub = $this->trial();
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP unavailable'));
        try {
            (new ExpireTrialsJob())->handle();
            $this->fail('Delivery should fail');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('pending deliveries', $e->getMessage());
        }
        $this->assertNull($sub->fresh()->trial_feedback_sent_at);
        $this->assertSame('cancelled', $sub->fresh()->status);
        Mail::swap(new \Illuminate\Mail\MailManager($this->app));
        Mail::fake();
        (new ExpireTrialsJob())->handle();
        Mail::assertSent(TrialExpiredMail::class, 1);
    }

    public function test_signed_feedback_works_without_login_and_preserves_first_submission(): void
    {
        $sub = $this->trial();
        $url = URL::temporarySignedRoute('trial-feedback.show', now()->addDays(30), ['subscription' => $sub->id]);
        $this->get($url)->assertOk()->assertSee('Wie war deine Testphase?')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->post($url, ['rating' => 4, 'reason' => 'features', 'comment' => 'Mehr Filter bitte'])->assertRedirect($url);
        $this->post($url, ['rating' => 1, 'reason' => 'price'])->assertRedirect($url);
        $this->assertSame(1, TrialFeedback::count());
        $this->assertSame(4, (int) TrialFeedback::first()->rating);
        $this->get($url)->assertOk()->assertSee('Danke für dein Feedback!');
        Mail::assertQueued(TrialFeedbackReceived::class, 1);
        Mail::assertQueued(TrialFeedbackReceived::class, fn ($mail) =>
            $mail->hasTo('mail@kreativ.team') && (int) $mail->feedback->rating === 4
        );
    }

    public function test_feedback_rejects_missing_tampered_expired_links_and_invalid_answers(): void
    {
        $sub = $this->trial();
        $url = URL::temporarySignedRoute('trial-feedback.show', now()->addDay(), ['subscription' => $sub->id]);
        $this->get(route('trial-feedback.show', $sub))->assertForbidden();
        $this->get($url . 'tampered')->assertForbidden();
        $this->postJson($url, ['rating' => 9, 'reason' => 'made_up'])->assertUnprocessable();
        $this->assertSame(0, TrialFeedback::count());
        Mail::assertNothingQueued();
        $this->travel(2)->days();
        $this->get($url)->assertForbidden();
        $this->post($url, ['rating' => 4, 'reason' => 'price'])->assertForbidden();
    }

    public function test_email_contains_signed_feedback_link_and_no_false_trial_or_deletion_claims(): void
    {
        $sub = $this->trial();
        $html = (new TrialExpiredMail($sub))->render();
        $this->assertStringContainsString('/trial-feedback/' . $sub->id, $html);
        $this->assertStringContainsString('signature=', $html);
        $this->assertStringNotContainsString('14-tägiger', $html);
        $this->assertStringNotContainsString('automatisch gelöscht', $html);
    }

    public function test_admin_access_is_preserved_and_admin_feedback_page_renders(): void
    {
        $sub = $this->trial();
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $sub->user->assignRole($role);
        $this->actingAs($sub->user)->get('/test-subscription-gate')->assertOk();
        TrialFeedback::create(['subscription_id' => $sub->id, 'rating' => 4, 'reason' => 'features', 'comment' => 'Mehr Filter']);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        \Livewire\Livewire::test(\App\Filament\Resources\TrialFeedbackResource\Pages\ListTrialFeedback::class)
            ->assertSee($sub->user->email)->assertSee('Mir fehlen Funktionen');
    }

    public function test_converted_users_get_no_expiry_mail_and_feedback_is_not_open_before_end(): void
    {
        $sub = $this->trial();
        Subscription::create([
            'user_id' => $sub->user_id, 'subscription_plan_id' => $sub->subscription_plan_id,
            'status' => 'active',
        ]);
        $future = $this->trial('+1 day');
        (new ExpireTrialsJob())->handle();
        Mail::assertNothingSent();
        $url = URL::temporarySignedRoute('trial-feedback.show', now()->addDays(30), ['subscription' => $future->id]);
        $this->get($url)->assertNotFound();
    }

    public function test_expired_users_see_paid_checkout_instead_of_another_trial(): void
    {
        $sub = $this->trial();
        $this->withoutVite();
        $this->actingAs($sub->user)->get(route('subscription.index'))
            ->assertOk()->assertSee('Mit PayPal starten')
            ->assertDontSee('href="' . route('subscription.start-trial') . '"', false);
    }

    public function test_feedback_notification_contains_customer_rating_reason_comment_and_admin_link(): void
    {
        $sub = $this->trial();
        $feedback = TrialFeedback::create([
            'subscription_id' => $sub->id, 'rating' => 5, 'reason' => 'features',
            'comment' => 'Mehr Filter bitte',
        ]);
        $text = (new TrialFeedbackReceived($feedback))->render();
        $this->assertStringContainsString($sub->user->email, $text);
        $this->assertStringContainsString('5 von 5', $text);
        $this->assertStringContainsString('Mir fehlen Funktionen', $text);
        $this->assertStringContainsString('Mehr Filter bitte', $text);
        $this->assertStringContainsString('/admin/trial-feedbacks', $text);
    }

    public function test_monthly_and_yearly_checkout_store_pending_and_redirect_to_paypal(): void
    {
        $sub = $this->trial();
        $plan = $sub->plan;
        $plan->update(['paypal_plan_id_monthly' => 'P-MONTH', 'paypal_plan_id_yearly' => 'P-YEAR']);
        foreach (['monthly' => 'P-MONTH', 'yearly' => 'P-YEAR'] as $cycle => $id) {
            $approval = 'https://www.sandbox.paypal.com/webapps/billing/subscriptions?ba_token=TEST-' . $cycle;
            $this->app->make(PayPalService::class)->shouldReceive('createSubscription')->once()
                ->with($id, route('subscription.success'), route('subscription.cancel'), Mockery::type('array'))
                ->andReturn(['id' => 'I-TEST-' . $cycle, 'approve_url' => $approval]);
            $this->actingAs($sub->user)->post(route('subscription.checkout'), [
                'plan_id' => $plan->id, 'billing_cycle' => $cycle,
            ])->assertRedirect($approval);
            $pending = Subscription::where('paypal_subscription_id', 'I-TEST-' . $cycle)->firstOrFail();
            $this->assertSame('pending', $pending->status);
            $this->assertSame('APPROVAL_PENDING', $pending->paypal_status);
            $this->assertFalse($pending->isActive());
            $this->assertSame($cycle, $pending->billing_cycle);
        }
    }

    public function test_missing_plan_id_does_not_call_paypal_or_create_subscription(): void
    {
        $sub = $this->trial();
        $this->app->make(PayPalService::class)->shouldNotReceive('createSubscription');
        $this->actingAs($sub->user)->from(route('subscription.index'))->post(route('subscription.checkout'), [
            'plan_id' => $sub->subscription_plan_id, 'billing_cycle' => 'monthly',
        ])->assertRedirect(route('subscription.index'))->assertSessionHasErrors('plan');
        $this->assertSame(1, Subscription::count());
    }

    public function test_reseeding_does_not_clear_existing_paypal_plan_ids(): void
    {
        $sub = $this->trial();
        $sub->plan->update(['paypal_plan_id_monthly' => 'P-KEEP-MONTH', 'paypal_plan_id_yearly' => 'P-KEEP-YEAR']);
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
        $this->assertSame('P-KEEP-MONTH', $sub->plan->fresh()->paypal_plan_id_monthly);
        $this->assertSame('P-KEEP-YEAR', $sub->plan->fresh()->paypal_plan_id_yearly);
    }
}
