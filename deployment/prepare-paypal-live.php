<?php

// Prepare the merchant's live catalog without creating subscriptions or switching the app.
if (PHP_SAPI !== 'cli' || ! in_array('--prepare', $argv, true)) {
    exit("Run from the app root: php deployment/prepare-paypal-live.php --prepare\n");
}

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Http;

$base = 'https://api-m.paypal.com';
$auth = Http::withBasicAuth(config('paypal.live.client_id'), config('paypal.live.client_secret'))
    ->asForm()->timeout(20)->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);
if (! $auth->successful() || ! $auth->json('access_token')) {
    throw new RuntimeException('PayPal Live authentication failed (HTTP ' . $auth->status() . ').');
}
$token = $auth->json('access_token');
$call = function (string $method, string $path, array $data = [], ?string $requestId = null) use ($base, $token): array {
    // A fresh request avoids accumulating PayPal idempotency headers between catalog entries.
    $request = Http::withToken($token)->acceptJson()->timeout(30);
    if ($requestId) {
        $request = $request->withHeaders(['PayPal-Request-Id' => $requestId]);
    }
    $response = $request->{$method}($base . $path, $data);
    if (! $response->successful()) {
        throw new RuntimeException($method . ' ' . $path . ': HTTP ' . $response->status() . ' ' . $response->json('name'));
    }
    return $response->json() ?? [];
};

$statePath = storage_path('app/private/paypal-live-setup.json');
if (! is_dir(dirname($statePath))) {
    mkdir(dirname($statePath), 0700, true);
}
$state = is_file($statePath) ? json_decode(file_get_contents($statePath), true, flags: JSON_THROW_ON_ERROR) : [
    'previous' => [
        'mode' => config('paypal.mode'),
        'product_id' => config('paypal.product_id'),
        'webhook_id' => config('paypal.webhook_id'),
        'plans' => SubscriptionPlan::get(['id', 'slug', 'paypal_plan_id_monthly', 'paypal_plan_id_yearly'])->toArray(),
    ],
];
$save = function () use (&$state, $statePath): void {
    file_put_contents($statePath, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    chmod($statePath, 0600);
};
$save();

if (empty($state['product_id'])) {
    $product = null;
    for ($page = 1; $page <= 100; $page++) {
        $list = $call('get', '/v1/catalogs/products', ['page_size' => 20, 'page' => $page, 'total_required' => 'true']);
        foreach ($list['products'] ?? [] as $candidate) {
            if ($candidate['name'] === 'SEOmaster') {
                $product = $candidate;
                break 2;
            }
        }
        if ($page >= ($list['total_pages'] ?? 1)) break;
    }
    $product ??= $call('post', '/v1/catalogs/products', [
        'name' => 'SEOmaster', 'description' => 'KI-gestützte SEO-Inhalte für Shopware 6',
        'type' => 'SERVICE', 'category' => 'SOFTWARE', 'home_url' => 'https://shopware-seomaster.com/',
    ], 'seomaster-live-product-v1');
    $state['product_id'] = $product['id'];
    $save();
}
echo 'Live product: ' . $state['product_id'] . PHP_EOL;

$remotePlans = $call('get', '/v1/billing/plans', ['product_id' => $state['product_id'], 'page_size' => 20]);
foreach (SubscriptionPlan::where('is_active', true)->get() as $plan) {
    foreach (['monthly' => 'MONTH', 'yearly' => 'YEAR'] as $cycle => $unit) {
        $expected = number_format($plan->{'price_' . $cycle}, 2, '.', '');
        $name = 'SEOmaster ' . $plan->name . ' ' . ucfirst($cycle);
        $id = $state['plans'][$plan->slug][$cycle] ?? null;
        if (! $id) {
            foreach ($remotePlans['plans'] ?? [] as $candidate) {
                if ($candidate['name'] === $name && $candidate['status'] === 'ACTIVE') {
                    $id = $candidate['id'];
                    break;
                }
            }
        }
        if (! $id) {
            $created = $call('post', '/v1/billing/plans', [
                'product_id' => $state['product_id'], 'name' => $name, 'status' => 'ACTIVE',
                'billing_cycles' => [[
                    'frequency' => ['interval_unit' => $unit, 'interval_count' => 1],
                    'tenure_type' => 'REGULAR', 'sequence' => 1, 'total_cycles' => 0,
                    'pricing_scheme' => ['fixed_price' => ['value' => $expected, 'currency_code' => 'EUR']],
                ]],
                'payment_preferences' => [
                    'auto_bill_outstanding' => true, 'payment_failure_threshold' => 3,
                    'setup_fee' => ['value' => '0', 'currency_code' => 'EUR'], 'setup_fee_failure_action' => 'CONTINUE',
                ],
            ], 'seomaster-live-' . $plan->slug . '-' . $cycle . '-v1');
            $id = $created['id'];
        }
        $details = $call('get', '/v1/billing/plans/' . $id);
        $billing = $details['billing_cycles'][0] ?? [];
        if ($details['status'] !== 'ACTIVE' || $details['product_id'] !== $state['product_id']
            || ($billing['frequency']['interval_unit'] ?? null) !== $unit
            || ($billing['frequency']['interval_count'] ?? null) !== 1
            || ($billing['pricing_scheme']['fixed_price']['currency_code'] ?? null) !== 'EUR'
            || number_format((float) ($billing['pricing_scheme']['fixed_price']['value'] ?? -1), 2, '.', '') !== $expected) {
            throw new RuntimeException('Live plan verification failed: ' . $name);
        }
        $state['plans'][$plan->slug][$cycle] = $id;
        $save();
        echo $name . ': ' . $id . ' / EUR ' . $expected . ' / verified' . PHP_EOL;
    }
}

$events = [
    'BILLING.SUBSCRIPTION.ACTIVATED', 'BILLING.SUBSCRIPTION.UPDATED', 'BILLING.SUBSCRIPTION.CANCELLED',
    'BILLING.SUBSCRIPTION.EXPIRED', 'BILLING.SUBSCRIPTION.SUSPENDED', 'BILLING.SUBSCRIPTION.PAYMENT.FAILED',
    'PAYMENT.SALE.COMPLETED', 'PAYMENT.SALE.REFUNDED',
];
$hooks = $call('get', '/v1/notifications/webhooks');
$webhook = null;
foreach ($hooks['webhooks'] ?? [] as $candidate) {
    if ($candidate['url'] === 'https://shopware-seomaster.com/webhooks/paypal') {
        $webhook = $candidate;
        break;
    }
}
if (! $webhook) {
    $webhook = $call('post', '/v1/notifications/webhooks', [
        'url' => 'https://shopware-seomaster.com/webhooks/paypal',
        'event_types' => array_map(fn ($name) => ['name' => $name], $events),
    ], 'seomaster-live-webhook-v1');
}
$webhook = $call('get', '/v1/notifications/webhooks/' . $webhook['id']);
if ($webhook['url'] !== 'https://shopware-seomaster.com/webhooks/paypal'
    || array_diff($events, array_column($webhook['event_types'], 'name'))) {
    throw new RuntimeException('Live webhook has missing events or an unexpected URL.');
}
$state['webhook_id'] = $webhook['id'];
$state['verified_at'] = now()->toIso8601String();
$save();
echo 'Live webhook verified: ' . $state['webhook_id'] . PHP_EOL;
echo 'Preparation complete. App mode and local plan IDs have NOT been changed.' . PHP_EOL;
