<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\SubscriptionCancelledMail;
use App\Mail\SubscriptionConfirmedMail;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private PayPalService $paypal) {}

    // ── Übersicht ─────────────────────────────────────────────────────────────

    public function index(): View
    {
        $user         = auth()->user();
        $plans        = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $subscription = $user->activeSubscription;
        $invoices     = Subscription::where('user_id', $user->id)
                            ->with('invoices')
                            ->get()
                            ->pluck('invoices')
                            ->flatten()
                            ->sortByDesc('created_at');

        return view('subscription.index', compact('plans', 'subscription', 'invoices'));
    }

    // ── Checkout → PayPal ─────────────────────────────────────────────────────

    public function checkout(Request $request)
    {
        $v = $request->validate([
            'plan_id'       => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan  = SubscriptionPlan::findOrFail($v['plan_id']);
        $cycle = $v['billing_cycle'];

        $paypalPlanId = $cycle === 'yearly'
            ? $plan->paypal_plan_id_yearly
            : $plan->paypal_plan_id_monthly;

        if (! $paypalPlanId) {
            return back()->withErrors(['plan' => 'Dieser Plan ist noch nicht für ' . $cycle . ' Abrechnung konfiguriert.']);
        }

        // Offene pending-Subs wegräumen
        Subscription::where('user_id', auth()->id())->where('status', 'pending')->delete();

        $result = $this->paypal->createSubscription(
            paypalPlanId:   $paypalPlanId,
            returnUrl:      route('subscription.success'),
            cancelUrl:      route('subscription.cancel'),
            subscriberData: [
                'email_address' => auth()->user()->email,
                'name'          => ['given_name' => auth()->user()->name],
            ],
        );

        if (isset($result['error'])) {
            return back()->withErrors(['paypal' => 'PayPal-Fehler: ' . $result['error']]);
        }

        Subscription::create([
            'user_id'                => auth()->id(),
            'subscription_plan_id'   => $plan->id,
            'paypal_subscription_id' => $result['id'],
            'paypal_status'          => 'APPROVAL_PENDING',
            'billing_cycle'          => $cycle,
            'status'                 => 'pending',
        ]);

        return redirect($result['approve_url']);
    }

    // ── PayPal Return (Erfolg) ────────────────────────────────────────────────

    public function success(Request $request)
    {
        $paypalSubId = $request->query('subscription_id');
        if (! $paypalSubId) {
            return redirect()->route('subscription.index')
                ->withErrors(['paypal' => 'Keine Subscription-ID erhalten.']);
        }

        $sub = Subscription::where('paypal_subscription_id', $paypalSubId)
                    ->where('user_id', auth()->id())
                    ->first();

        if (! $sub) {
            return redirect()->route('subscription.index')
                ->withErrors(['paypal' => 'Subscription nicht gefunden.']);
        }

        $details = $this->paypal->getSubscription($paypalSubId);

        if (($details['status'] ?? '') === 'ACTIVE') {
            $sub->update([
                'status'               => 'active',
                'paypal_status'        => 'ACTIVE',
                'current_period_start' => now(),
                'current_period_end'   => $sub->billing_cycle === 'yearly'
                    ? now()->addYear()
                    : now()->addMonth(),
            ]);

            try {
                Mail::to(auth()->user())->send(new SubscriptionConfirmedMail($sub));
            } catch (\Throwable $e) {
                Log::warning('SubscriptionConfirmedMail failed', ['error' => $e->getMessage()]);
            }

            return redirect()->route('dashboard')
                ->with('success', '🎉 Abonnement aktiv! Willkommen bei SEOmaster.');
        }

        return redirect()->route('subscription.index')
            ->with('warning', '⏳ Dein Abonnement wird gerade aktiviert. Bitte kurz warten.');
    }

    // ── PayPal Abbruch ────────────────────────────────────────────────────────

    public function cancel()
    {
        Subscription::where('user_id', auth()->id())->where('status', 'pending')->delete();

        return redirect()->route('subscription.index')
            ->with('info', 'Checkout abgebrochen. Dein aktueller Plan bleibt unverändert.');
    }

    // ── Abo kündigen ──────────────────────────────────────────────────────────

    public function cancelPlan(Request $request)
    {
        $user = auth()->user();
        $sub  = $user->activeSubscription;

        if (! $sub) {
            return back()->withErrors(['sub' => 'Kein aktives Abonnement gefunden.']);
        }

        if ($sub->status === 'trial') {
            $sub->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            return redirect()->route('subscription.index')
                ->with('success', 'Trial beendet.');
        }

        if (! $sub->paypal_subscription_id) {
            return back()->withErrors(['sub' => 'Keine PayPal-Subscription-ID gefunden.']);
        }

        $ok = $this->paypal->cancelSubscription($sub->paypal_subscription_id, 'Benutzer hat Kündigung beantragt');

        if (! $ok) {
            return back()->withErrors(['paypal' => 'Kündigung bei PayPal fehlgeschlagen. Bitte kontaktiere den Support.']);
        }

        $sub->update(['status' => 'cancelled', 'paypal_status' => 'CANCELLED', 'cancelled_at' => now()]);

        try {
            Mail::to($user)->send(new SubscriptionCancelledMail($sub));
        } catch (\Throwable $e) {
            Log::warning('SubscriptionCancelledMail failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Abo gekündigt. Es läuft bis ' . $sub->current_period_end?->format('d.m.Y') . '.');
    }

    // ── PayPal Webhook ─────────────────────────────────────────────────────────

    public function webhook(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! $this->paypal->verifyWebhookSignature($request)) {
            Log::warning('PayPal webhook: invalid signature', ['event' => $request->input('event_type')]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $eventType = $request->input('event_type');
        $resource  = $request->input('resource', []);

        Log::info('PayPal webhook', ['event_type' => $eventType, 'id' => $resource['id'] ?? null]);

        try {
            match ($eventType) {
                'BILLING.SUBSCRIPTION.ACTIVATED'       => $this->handleActivated($resource),
                'BILLING.SUBSCRIPTION.UPDATED'         => $this->handleUpdated($resource),
                'BILLING.SUBSCRIPTION.EXPIRED'         => $this->handleExpired($resource),
                'BILLING.SUBSCRIPTION.CANCELLED'       => $this->handleCancelled($resource),
                'BILLING.SUBSCRIPTION.SUSPENDED'       => $this->handleSuspended($resource),
                'BILLING.SUBSCRIPTION.PAYMENT.FAILED'  => $this->handlePaymentFailed($resource),
                'PAYMENT.SALE.COMPLETED'               => $this->handlePaymentCompleted($resource),
                'PAYMENT.SALE.REFUNDED'                => $this->handlePaymentRefunded($resource),
                default                                => null,
            };
        } catch (\Throwable $e) {
            Log::error('PayPal webhook handler error', ['event' => $eventType, 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ── Private Webhook Handlers ──────────────────────────────────────────────

    private function handleActivated(array $r): void
    {
        $sub = $this->find($r['id'] ?? '');
        if (! $sub) return;
        $sub->update([
            'status'               => 'active',
            'paypal_status'        => 'ACTIVE',
            'current_period_start' => now(),
            'current_period_end'   => $sub->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
        ]);
    }

    private function handleUpdated(array $r): void
    {
        $sub = $this->find($r['id'] ?? '');
        $sub?->update(['paypal_status' => $r['status'] ?? null]);
    }

    private function handleExpired(array $r): void
    {
        $this->find($r['id'] ?? '')
            ?->update(['status' => 'cancelled', 'paypal_status' => 'EXPIRED', 'cancelled_at' => now()]);
    }

    private function handleCancelled(array $r): void
    {
        $sub = $this->find($r['id'] ?? '');
        if (! $sub) return;
        $sub->update([
            'status'        => 'cancelled',
            'paypal_status' => 'CANCELLED',
            'cancelled_at'  => $sub->cancelled_at ?? now(),
        ]);
    }

    private function handleSuspended(array $r): void
    {
        $this->find($r['id'] ?? '')
            ?->update(['status' => 'suspended', 'paypal_status' => 'SUSPENDED']);
    }

    private function handlePaymentFailed(array $r): void
    {
        $sub = $this->find($r['billing_agreement_id'] ?? $r['id'] ?? '');
        if (! $sub) return;

        SubscriptionInvoice::create([
            'subscription_id'       => $sub->id,
            'paypal_transaction_id' => $r['id'] ?? null,
            'amount'                => (float) ($r['amount']['value'] ?? $r['amount']['total'] ?? 0),
            'currency'              => strtoupper($r['amount']['currency_code'] ?? $r['amount']['currency'] ?? 'EUR'),
            'status'                => 'failed',
            'paid_at'               => null,
        ]);
    }

    private function handlePaymentCompleted(array $r): void
    {
        $billingId = $r['billing_agreement_id'] ?? null;
        if (! $billingId) return;

        $sub = $this->find($billingId);
        if (! $sub) return;

        // Duplicate guard
        if (SubscriptionInvoice::where('paypal_transaction_id', $r['id'])->exists()) return;

        SubscriptionInvoice::create([
            'subscription_id'       => $sub->id,
            'paypal_transaction_id' => $r['id'],
            'amount'                => (float) ($r['amount']['total'] ?? $r['amount']['value'] ?? 0),
            'currency'              => strtoupper($r['amount']['currency'] ?? $r['amount']['currency_code'] ?? 'EUR'),
            'status'                => 'paid',
            'paid_at'               => now(),
        ]);

        $sub->update([
            'status'               => 'active',
            'paypal_status'        => 'ACTIVE',
            'current_period_start' => now(),
            'current_period_end'   => $sub->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
        ]);
    }

    private function handlePaymentRefunded(array $r): void
    {
        $saleId = $r['sale_id'] ?? $r['id'] ?? null;
        if ($saleId) {
            SubscriptionInvoice::where('paypal_transaction_id', $saleId)->update(['status' => 'refunded']);
        }
    }

    private function find(string $paypalId): ?Subscription
    {
        return $paypalId ? Subscription::where('paypal_subscription_id', $paypalId)->first() : null;
    }
}
