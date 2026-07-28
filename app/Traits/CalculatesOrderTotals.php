<?php

namespace App\Traits;

trait CalculatesOrderTotals
{
    /**
     * Get the tax rate (9% by default).
     */
    public function getTaxRate(): float
    {
        return config('shop.tax_rate', 0.09);
    }

    /**
     * Get the shipping threshold (500,000 Toman by default).
     */
    public function getShippingThreshold(): float
    {
        return config('shop.shipping.threshold', 500000);
    }

    /**
     * Get the flat shipping cost (25,000 Toman by default).
     */
    public function getFlatShippingCost(): float
    {
        return config('shop.shipping.cost', 25000);
    }

    /**
     * Calculate totals based on subtotal.
     */
    public function calculateAmounts(float $subtotal): array
    {
        if ($subtotal == 0) {
            return [
                'tax_amount' => 0,
                'shipping_amount' => 0,
            ];
        }

        $shipping = $subtotal > $this->getShippingThreshold() ? 0 : $this->getFlatShippingCost();

        return [
            'tax_amount' => 0,
            'shipping_amount' => $shipping,
        ];
    }
}
