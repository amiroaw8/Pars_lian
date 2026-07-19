<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function initiatePayment(Order $order)
    {
        if ($order->status !== OrderStatus::PENDING || $order->payment_status !== PaymentStatus::PENDING) {
            throw new Exception('Order is not in a payable state.');
        }

        $amount = (int) $order->total;
        $callbackUrl = route('payment.callback', ['order' => $order->id]);

        // Create pending transaction record
        $transaction = $order->transactions()->create([
            'amount' => $amount,
            'gateway' => 'zarinpal', // This could be dynamic if we support multiple gateways
            'status' => 'pending',
            'description' => 'Payment for Order #' . $order->order_number,
        ]);

        try {
            $result = $this->gateway->request($order, $amount, $callbackUrl);

            if ($result['success']) {
                $transaction->update([
                    'transaction_id' => $result['transaction_id'], // Authority
                    'gateway_response' => json_encode($result),
                ]);

                return $result['payment_url'];
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($result),
                ]);
                throw new Exception($result['message']);
            }
        } catch (Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => json_encode(['error' => $e->getMessage()]),
            ]);
            throw $e;
        }
    }

    public function verifyPayment(Order $order, string $authority, string $status)
    {
        $transaction = $order->transactions()
            ->where('transaction_id', $authority)
            ->latest()
            ->first();

        if (!$transaction) {
            throw new Exception('Transaction not found.');
        }

        if ($status !== 'OK') {
            $transaction->update(['status' => 'failed']);
            $order->update(['payment_status' => PaymentStatus::FAILED]);
            return false;
        }

        $result = $this->gateway->verify($order, (int) $transaction->amount, $authority);

        if ($result['success']) {
            DB::transaction(function () use ($order, $transaction, $result) {
                $transaction->update([
                    'status' => 'success',
                    'reference_id' => $result['reference_id'],
                    'payment_date' => now(),
                    'gateway_response' => json_encode($result),
                ]);

                $order->update([
                    'status' => OrderStatus::PROCESSING,
                    'payment_status' => PaymentStatus::PAID,
                ]);
            });

            return true;
        } else {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => json_encode($result),
            ]);
            $order->update(['payment_status' => PaymentStatus::FAILED]);
            return false;
        }
    }
}
