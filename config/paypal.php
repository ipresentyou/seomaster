<?php

/*
 |--------------------------------------------------------------------------
 | PayPal Configuration
 |--------------------------------------------------------------------------
 |
 | Konfiguration für srmklive/paypal v3.
 |
 | Alle sensitiven Werte kommen aus der .env Datei.
 | Billing Plans werden direkt im PayPal Dashboard angelegt.
 |
 */

return [

    'mode' => env('PAYPAL_MODE', 'sandbox'),   // 'sandbox' | 'live'

    'sandbox' => [
        'client_id'     => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'app_id'        => 'APP-80W284485P519543T',
    ],

    'live' => [
        'client_id'     => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id'        => env('PAYPAL_LIVE_APP_ID', ''),
    ],

    // Webhook ID aus dem PayPal Dashboard (Webhooks → dein Endpoint)
    'webhook_id' => env('PAYPAL_WEBHOOK_ID', ''),

    // PayPal Product ID (optional, für listBillingPlans)
    'product_id' => env('PAYPAL_PRODUCT_ID', ''),

    // Validierungsregeln (für srmklive interne Validierung)
    'validate_ssl'    => true,
    'currency'        => env('PAYPAL_CURRENCY', 'EUR'),
    'notify_url'      => '',                // Legacy IPN (nicht nötig bei Webhooks)
    'locale'          => 'de_DE',

    'payment_action'  => 'Sale',
    'invoice_prefix'  => env('PAYPAL_INVOICE_PREFIX', 'LAVARELL-'),
    'billing_type'    => 'MerchantInitiatedBilling',
];
