<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Exception;

use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure user owns the order or has permission
        if ($user->id !== $order->user_id && !$user->can('manage_orders')) {
            abort(403);
        }

        try {
            $paymentUrl = $this->paymentService->initiatePayment($order);
            return redirect($paymentUrl);
        } catch (Exception $e) {
            return back()->with('error', 'خطا در ایجاد تراکنش: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $authority = $request->query('Authority');
        $status = $request->query('Status');

        try {
            $transaction = \App\Models\PaymentTransaction::with('order')->where('transaction_id', $authority)->firstOrFail();
            $order = $transaction->order;

            $success = $this->paymentService->verifyPayment($order, $authority, $status);

            if ($success) {
                return to_route('checkout.success', $order->order_number)
                    ->with('success', 'پرداخت و ثبت سفارش شما با موفقیت انجام شد.');
            }

            return to_route('checkout.success', $order->order_number)
                ->with('error', 'پرداخت ناموفق بود. سفارش ثبت شده و می‌توانید مجدداً پرداخت کنید.');
        } catch (Exception $e) {
            // Provide a generic error fallback in case order resolution fails before verifying payment
            // Or if we successfully got order, redirect to it:
            if (isset($order)) {
                return to_route('customer.orders.shop-show', $order)->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
            }
            return redirect('/')->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
        }
    }
}
