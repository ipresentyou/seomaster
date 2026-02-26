<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

/**
 * PayPalService
 *
 * Wraps srmklive/paypal for subscription billing.
 *
 * Responsibilities:
 *   - OAuth token management (cached)
 *   - Create / fetch / cancel subscriptions
 *   - Webhook signature verification
 *   - Raw REST calls for edge cases
 */
class PayPalService
{
    private PayPalClient $client;
    private string $mode;

    // ── Boot ────────────────────────────────────────────────────────────────

    public function __construct()
    {
        $this->mode   = config('paypal.mode', 'sandbox');
        $this->client = new PayPalClient;
        $this->client->setApiCredentials(config('paypal'));
        $this->client->getAccessToken();
    }

    // ── Subscription Lifecycle ───────────────────────────────────────────────

    /**
     * Create a new subscription on PayPal.
     *
     * @return array{id: string, approve_url: string}|array{error: string}
     */
    public function createSubscription(
        string $paypalPlanId,
        string $returnUrl,
        string $cancelUrl,
        array  $subscriberData = []
    ): array {
        $payload = [
            'plan_id'             => $paypalPlanId,
            'application_context' => [
                'return_url'          => $returnUrl,
                'cancel_url'          => $cancelUrl,
                'shipping_preference' => 'NO_SHIPPING',
                'user_action'         => 'SUBSCRIBE_NOW',
                'brand_name'          => config('app.name', 'SEOmaster'),
                'locale'              => 'de-DE',
            ],
        ];

        if (! empty($subscriberData)) {
            $payload['subscriber'] = $subscriberData;
        }

        try {
            $response = $this->client->createSubscription($payload);
        } catch (\Throwable $e) {
            Log::error('PayPal createSubscription exception', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }

        if (! isset($response['id'])) {
            $msg = $response['message'] ?? 'PayPal hat keine Subscription-ID zurückgegeben.';
            Log::warning('PayPal createSubscription failed', $response);
            return ['error' => $msg];
        }

        $approveUrl = collect($response['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approveUrl) {
            return ['error' => 'PayPal approval URL fehlt.'];
        }

        return [
            'id'          => $response['id'],
            'approve_url' => $approveUrl,
        ];
    }

    /**
     * Fetch full subscription details from PayPal.
     *
     * @return array|null  null on failure
     */
    public function getSubscription(string $paypalSubscriptionId): ?array
    {
        try {
            $details = $this->client->showSubscriptionDetails($paypalSubscriptionId);
            return isset($details['id']) ? $details : null;
        } catch (\Throwable $e) {
            Log::error('PayPal getSubscription failed', [
                'id'      => $paypalSubscriptionId,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cancel a subscription on PayPal.
     */
    public function cancelSubscription(string $paypalSubscriptionId, string $reason = 'User requested'): bool
    {
        try {
            $this->client->cancelSubscription($paypalSubscriptionId, $reason);
            return true;
        } catch (\Throwable $e) {
            Log::error('PayPal cancelSubscription failed', [
                'id'      => $paypalSubscriptionId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Suspend a subscription on PayPal.
     */
    public function suspendSubscription(string $paypalSubscriptionId, string $reason = 'Suspended'): bool
    {
        try {
            $this->client->suspendSubscription($paypalSubscriptionId, $reason);
            return true;
        } catch (\Throwable $e) {
            Log::error('PayPal suspendSubscription failed', [
                'id'      => $paypalSubscriptionId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Reactivate a suspended subscription.
     */
    public function activateSubscription(string $paypalSubscriptionId, string $reason = 'Reactivated'): bool
    {
        try {
            $this->client->activateSubscription($paypalSubscriptionId, $reason);
            return true;
        } catch (\Throwable $e) {
            Log::error('PayPal activateSubscription failed', [
                'id'      => $paypalSubscriptionId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // ── Billing Plans ────────────────────────────────────────────────────────

    /**
     * List all billing plans from PayPal (for syncing with DB).
     * Cached for 1 hour.
     */
    public function listBillingPlans(int $pageSize = 20): array
    {
        return Cache::remember('paypal_billing_plans', 3600, function () use ($pageSize) {
            try {
                $response = $this->client->listPlans([
                    'product_id' => config('paypal.product_id'),
                    'page_size'  => $pageSize,
                    'status'     => 'ACTIVE',
                ]);
                return $response['plans'] ?? [];
            } catch (\Throwable $e) {
                Log::error('PayPal listBillingPlans failed', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    // ── Webhook Verification ─────────────────────────────────────────────────

    /**
     * Verify a PayPal webhook signature.
     *
     * Uses PayPal's /v1/notifications/verify-webhook-signature endpoint.
     * Returns true if verified, false otherwise.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $webhookId = config('paypal.webhook_id');

        if (! $webhookId) {
            // No webhook ID configured — skip verification in dev
            if ($this->mode === 'sandbox') {
                Log::debug('PayPal webhook signature skipped (no webhook_id, sandbox mode)');
                return true;
            }
            Log::error('PayPal webhook_id not configured in production!');
            return false;
        }

        $baseUrl = $this->mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->post("{$baseUrl}/v1/notifications/verify-webhook-signature", [
                    'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
                    'cert_url'          => $request->header('PAYPAL-CERT-URL'),
                    'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
                    'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
                    'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                    'webhook_id'        => $webhookId,
                    'webhook_event'     => $request->json()->all(),
                ]);

            $verificationStatus = $response->json('verification_status');

            if ($verificationStatus !== 'SUCCESS') {
                Log::warning('PayPal webhook signature verification failed', [
                    'status' => $verificationStatus,
                    'event'  => $request->input('event_type'),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('PayPal webhook verification exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    // ── Token Helper ─────────────────────────────────────────────────────────

    /**
     * Get a valid OAuth access token (cached for 55 minutes).
     */
    private function getAccessToken(): string
    {
        return Cache::remember('paypal_access_token', 3300, function () {
            $baseUrl  = $this->mode === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $clientId     = config("paypal.{$this->mode}.client_id");
            $clientSecret = config("paypal.{$this->mode}.client_secret");

            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            return $response->json('access_token') ?? '';
        });
    }
}
