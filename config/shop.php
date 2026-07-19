<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Configuration
    |--------------------------------------------------------------------------
    */

    'tax_rate' => env('SHOP_TAX_RATE', 0),
    
    'shipping' => [
        'threshold' => env('SHOP_SHIPPING_THRESHOLD', 500000), // Free shipping over 500,000 Toman
        'cost' => env('SHOP_SHIPPING_COST', 25000), // Standard shipping cost (Toman)
    ],

    'currency' => env('SHOP_CURRENCY', 'IRT'),
];
