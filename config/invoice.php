<?php

return [
    'seller' => [
        'company'    => env('INVOICE_SELLER_COMPANY', ''),
        'name'       => env('INVOICE_SELLER_NAME', ''),
        'address'    => env('INVOICE_SELLER_ADDRESS', ''),
        'zip'        => env('INVOICE_SELLER_ZIP', ''),
        'city'       => env('INVOICE_SELLER_CITY', ''),
        'country'    => env('INVOICE_SELLER_COUNTRY', 'DE'),
        'vat_id'     => env('INVOICE_SELLER_VAT_ID', ''),
        'tax_number' => env('INVOICE_SELLER_TAX_NUMBER', ''),
        'email'      => env('INVOICE_SELLER_EMAIL', ''),
        'iban'       => env('INVOICE_SELLER_IBAN', ''),
        'bic'        => env('INVOICE_SELLER_BIC', ''),
        'bank'       => env('INVOICE_SELLER_BANK', ''),
        'website'    => env('INVOICE_SELLER_WEBSITE', ''),
        'director'   => env('INVOICE_SELLER_DIRECTOR', ''),
        'register_court' => env('INVOICE_SELLER_REGISTER_COURT', ''),
        'register_number' => env('INVOICE_SELLER_REGISTER_NUMBER', ''),
    ],
    'enabled'        => (bool) env('INVOICE_ENABLED', false),
    'tax_rate'       => (float) env('INVOICE_TAX_RATE', 19),
    'payment_days'   => (int) env('INVOICE_PAYMENT_DAYS', 14),
    'prefix'         => env('INVOICE_PREFIX', 'RE'),
];
