<?php

return [
    'mode' => env('ZATCA_MODE', 'development'),
    'environment' => env('ZATCA_ENVIRONMENT', 'sandbox'),
    'api_url' => env('ZATCA_API_URL', 'https://api.zatca.gov.sa/v1'),
    'otp' => env('ZATCA_OTP', ''),
    'username' => env('ZATCA_USERNAME', ''),
    'password' => env('ZATCA_PASSWORD', ''),
    'certificate_path' => env('ZATCA_CERTIFICATE_PATH', storage_path('zatca/certificate.pem')),
    'private_key_path' => env('ZATCA_PRIVATE_KEY_PATH', storage_path('zatca/private.key')),
    'vat_rate' => env('VAT_RATE', 0.15),
    'vat_enabled' => env('VAT_ENABLED', true),
    'invoice_prefix' => env('INVOICE_PREFIX', 'INV'),
    'invoice_series_start' => env('INVOICE_SERIES_START', 1000),
    'qr_version' => env('INVOICE_QR_VERSION', 2),
    'timeout' => 30,
    'retry_attempts' => 3,
    'supported_currencies' => ['SAR'],
    'hash_algorithm' => 'sha256',
];
