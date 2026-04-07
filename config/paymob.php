<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paymob (Accept) Egypt
    |--------------------------------------------------------------------------
    |
    | Credentials come from the Paymob dashboard:
    |  - API Key: Dashboard → Settings → Account Info
    |  - Integration ID: Dashboard → Developers → Payment Integrations
    |  - iFrame ID: Dashboard → Developers → iFrames
    |  - HMAC Secret: Dashboard → Settings → Account Info → HMAC
    |
    */

    'api_key' => env('PAYMOB_API_KEY'),
    'integration_id' => env('PAYMOB_INTEGRATION_ID'),
    'iframe_id' => env('PAYMOB_IFRAME_ID'),
    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),

    // Base URL — EG region
    'base_url' => env('PAYMOB_BASE_URL', 'https://accept.paymob.com/api'),

    // Currency must match the integration configured in Paymob (EGP for Egypt)
    'currency' => env('PAYMOB_CURRENCY', 'EGP'),

    // Testing toggle — when true, PaymobService will short-circuit and mark payments as succeeded locally
    'test_mode' => env('PAYMOB_TEST_MODE', false),
];
