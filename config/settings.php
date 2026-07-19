<?php

return [
    'cache_ttl' => env('CACHE_TTL', 300),
    'inventory_cache_ttl' => env('INVENTORY_CACHE_TTL', 600),
    'customer_cache_ttl' => env('CUSTOMER_CACHE_TTL', 3600),
    'revenue_cache_ttl' => env('REVENUE_CACHE_TTL', 86400),
    'accounting_cache_ttl' => env('ACCOUNTING_CACHE_TTL', 3600),
    'shop_cache_ttl' => env('SHOP_CACHE_TTL', 3600),
    'featured_products_cache_ttl' => env('FEATURED_PRODUCTS_CACHE_TTL', 1800),
    'device_types_cache_ttl' => env('DEVICE_TYPES_CACHE_TTL', 3600),
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 5),
];
