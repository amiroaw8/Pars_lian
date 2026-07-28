<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Exception;

class PaymentService
{
    protected ?PaymentGatewayInterface $customGateway = null;

    public function __construct(?PaymentGatewayInterface $customGateway = null)
    {
        $this->customGateway = $customGateway;
    }

    /**
     * Initiate payment for an order using Shetabit Payment or custom mock gateway.
     */
    public function initiatePayment(Order $order, ?string $driver = null)
    {
        if ($order->status !== OrderStatus::PENDING || $order->payment_status !== PaymentStatus::PENDING) {
            throw new Exception('سفارش در وضعیت قابل پرداخت قرار ندارد.');
        }

        // Check if custom mock gateway is provided
        if ($this->customGateway) {
            $gateway = $this->customGateway;
            $amount = (int) $order->total;
            $callbackUrl = route('payment.callback', ['order' => $order->id]);

            $transaction = $order->transactions()->create([
                'amount' => $amount,
                'gateway' => 'zarinpal',
                'status' => 'pending',
                'description' => 'پرداخت سفارش #' . $order->order_number,
            ]);

            $result = $gateway->request($order, $amount, $callbackUrl);

            if ($result['success']) {
                $transaction->update([
                    'transaction_id' => $result['transaction_id'],
                    'gateway_response' => json_encode($result),
                ]);

                return redirect()->away($result['payment_url']);
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($result),
                ]);
                throw new Exception($result['message'] ?? 'خطا در ارتباط با درگاه');
            }
        }

        // Determine driver
        if (!$driver) {
            $driver = $order->payment_gateway ?? PaymentGatewayManager::getSettings()['default'] ?? 'zarinpal';
        }

        // Configure Shetabit driver from dynamic database settings
        PaymentGatewayManager::configureDriver($driver);

        $amount = (int) $order->total;
        $callbackUrl = route('payment.callback', ['order' => $order->id, 'driver' => $driver]);

        $transaction = $order->transactions()->create([
            'amount' => $amount,
            'gateway' => $driver,
            'status' => 'pending',
            'description' => 'پرداخت سفارش #' . $order->order_number,
        ]);

        try {
            $invoice = (new Invoice)->amount($amount);
            $invoice->detail([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'mobile' => $order->shipping_phone,
                'email' => $order->shipping_email,
            ]);

            return Payment::via($driver)
                ->callbackUrl($callbackUrl)
                ->purchase($invoice, function ($driverObj, $transactionId) use ($transaction) {
                    $transaction->update([
                        'transaction_id' => (string) $transactionId,
                        'gateway_response' => json_encode(['transaction_id' => $transactionId]),
                    ]);
                })
                ->pay();
        } catch (Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => json_encode(['error' => $e->getMessage()]),
            ]);
            Log::error('Payment initiation error', ['order' => $order->id, 'driver' => $driver, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Verify payment on callback.
     */
    public function verifyPayment(Order $order, Request $request)
    {
        $authority = $request->query('Authority')
            ?? $request->input('Authority')
            ?? $request->input('trackId')
            ?? $request->input('trans_id')
            ?? $request->input('token')
            ?? $request->input('refNum');

        $transaction = null;
        if ($authority) {
            $transaction = $order->transactions()->where('transaction_id', $authority)->latest()->first();
        }
        if (!$transaction) {
            $transaction = $order->transactions()->where('status', 'pending')->latest()->first();
        }

        if (!$transaction) {
            throw new Exception('تراکنش معتبری برای این سفارش یافت نشد.');
        }

        // Support test mock gateway
        if ($this->customGateway) {
            $gateway = $this->customGateway;
            $status = $request->query('Status') ?? $request->input('Status') ?? 'NOK';

            if ($status !== 'OK') {
                $transaction->update(['status' => 'failed']);
                $order->update(['payment_status' => PaymentStatus::FAILED]);
                return false;
            }

            $result = $gateway->verify($order, (int) $transaction->amount, $authority ?: $transaction->transaction_id);

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
                $transaction->update(['status' => 'failed', 'gateway_response' => json_encode($result)]);
                $order->update(['payment_status' => PaymentStatus::FAILED]);
                return false;
            }
        }

        $driver = $transaction->gateway ?: 'zarinpal';
        PaymentGatewayManager::configureDriver($driver);

        $amount = (int) $transaction->amount;
        $transactionId = $transaction->transaction_id ?: $authority;

        $statusParam = $request->query('Status') ?? $request->input('Status') ?? $request->input('status');
        if ($statusParam && strtoupper((string)$statusParam) === 'NOK') {
            $transaction->update(['status' => 'failed', 'gateway_response' => json_encode($request->all())]);
            $order->update(['payment_status' => PaymentStatus::FAILED]);
            return false;
        }

        try {
            $receipt = Payment::via($driver)
                ->amount($amount)
                ->transactionId($transactionId)
                ->verify();

            $referenceId = (string) $receipt->getReferenceId();

            DB::transaction(function () use ($order, $transaction, $receipt, $referenceId, $request) {
                $transaction->update([
                    'status' => 'success',
                    'reference_id' => $referenceId,
                    'payment_date' => now(),
                    'gateway_response' => json_encode([
                        'reference_id' => $referenceId,
                        'driver' => $receipt->getDriver(),
                        'raw' => $request->all(),
                    ]),
                ]);

                $order->update([
                    'status' => OrderStatus::PROCESSING,
                    'payment_status' => PaymentStatus::PAID,
                ]);
            });

            return true;
        } catch (Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => json_encode([
                    'error' => $e->getMessage(),
                    'raw' => $request->all(),
                ]),
            ]);

            $order->update(['payment_status' => PaymentStatus::FAILED]);
            Log::error('Payment verification failed', ['order' => $order->id, 'driver' => $driver, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
