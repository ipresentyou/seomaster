<?php

return [
    'seller' => [
        'company'    => env('INVOICE_SELLER_COMPANY', 'SEOmaster GmbH'),
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
    ],
    'tax_rate'       => (float) env('INVOICE_TAX_RATE', 19),
    'payment_days'   => (int) env('INVOICE_PAYMENT_DAYS', 14),
    'prefix'         => env('INVOICE_PREFIX', 'RE'),
];
