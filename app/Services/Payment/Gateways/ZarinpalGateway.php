<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class ZarinpalGateway implements PaymentGatewayInterface
{
    protected string $merchantId;
    protected string $baseUrl = 'https://api.zarinpal.com/pg/v4/payment';

    public function __construct()
    {
        $this->merchantId = (string) config('services.zarinpal.merchant_id');

        if ($this->merchantId === '') {
            throw new Exception('Zarinpal merchant id is not configured.');
        }
    }

    public function request(Order $order, int $amount, string $callbackUrl): array
    {
        $response = Http::post($this->baseUrl . '/request.json', [
            'merchant_id' => $this->merchantId,
            'amount' => $amount, // Toman
            'description' => "Order #{$order->id}",
            'callback_url' => $callbackUrl,
            'metadata' => [
                'order_id' => $order->id,
                'email' => $order->user->email ?? '',
                'mobile' => $order->user->phone ?? '',
            ],
        ]);

        if ($response->successful() && isset($response['data']['code']) && $response['data']['code'] == 100) {
            return [
                'success' => true,
                'transaction_id' => $response['data']['authority'],
                'payment_url' => "https://www.zarinpal.com/pg/StartPay/" . $response['data']['authority'],
            ];
        }

        return [
            'success' => false,
            'message' => $response['errors']['message'] ?? 'Payment request failed',
        ];
    }

    public function verify(Order $order, int $amount, string $authority): array
    {
        $response = Http::post($this->baseUrl . '/verify.json', [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'authority' => $authority,
        ]);

        if ($response->successful() && isset($response['data']['code']) && ($response['data']['code'] == 100 || $response['data']['code'] == 101)) {
            return [
                'success' => true,
                'reference_id' => $response['data']['ref_id'],
                'card_pan' => $response['data']['card_pan'] ?? null,
            ];
        }

        return [
            'success' => false,
            'message' => $response['errors']['message'] ?? 'Payment verification failed',
        ];
    }
}
