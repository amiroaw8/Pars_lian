<?php

namespace App\Http\Middleware;

use App\Support\ShopFormat;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeMoneyInputs
{
    private const MONEY_KEYS = [
        'price',
        'sale_price',
        'amount',
        'cost',
        'service_cost',
        'unit_price',
        'min_price',
        'max_price',
        'subtotal',
        'total',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            $request->replace($this->normalizeData($request->all()));
        }

        return $next($request);
    }

    private function normalizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizeData($value);
                continue;
            }

            if ($this->isMoneyKey((string) $key) && (is_string($value) || is_numeric($value))) {
                $data[$key] = ShopFormat::toIntegerAmount($value);
            }
        }

        return $data;
    }

    private function isMoneyKey(string $key): bool
    {
        if (preg_match('/quantity|stock|percent|tax|sort_order|page$/i', $key)) {
            return false;
        }

        if (in_array($key, self::MONEY_KEYS, true)) {
            return true;
        }

        return (bool) preg_match('/_(price|amount|cost)$/i', $key);
    }
}
