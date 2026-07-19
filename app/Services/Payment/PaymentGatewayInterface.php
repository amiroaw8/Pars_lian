<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Request payment from the gateway.
     *
     * @param Order $order
     * @param int $amount Amount in Rails (or default currency)
     * @param string $callbackUrl
     * @return array Returns ['url' => string, 'transaction_id' => string]
     */
    public function request(Order $order, int $amount, string $callbackUrl): array;

    /**
     * Verify payment with the gateway.
     *
     * @param Order $order
     * @param int $amount
     * @param string $authority
     * @return array Returns ['status' => 'success'|'failed', 'reference_id' => ?string, 'message' => ?string]
     */
    public function verify(Order $order, int $amount, string $authority): array;
}
