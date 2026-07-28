<?php

namespace App\Http\Controllers;

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

    public function pay(Order $order, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $order->user_id && !$user->can('manage_orders')) {
            abort(403);
        }

        try {
            $driver = $request->query('driver') ?? $request->input('driver') ?? $order->payment_gateway;
            $payResponse = $this->paymentService->initiatePayment($order, $driver);

            if (is_string($payResponse)) {
                return redirect()->away($payResponse);
            }

            if ($payResponse instanceof \Shetabit\Multipay\RedirectionForm) {
                if (strtoupper($payResponse->getMethod()) === 'GET' && empty($payResponse->getInputs())) {
                    return redirect()->away($payResponse->getAction());
                }

                return response($payResponse->render());
            }

            return $payResponse;
        } catch (Exception $e) {
            return back()->with('error', 'خطا در ایجاد تراکنش: ' . $e->getMessage());
        }
    }

    public function callback(Request $request, ?Order $order = null)
    {
        try {
            if (!$order || !$order->exists) {
                $authority = $request->query('Authority')
                    ?? $request->input('Authority')
                    ?? $request->input('trackId')
                    ?? $request->input('trans_id')
                    ?? $request->input('token');

                $transaction = \App\Models\PaymentTransaction::with('order')
                    ->where('transaction_id', $authority)
                    ->latest()
                    ->first();

                if ($transaction && $transaction->order) {
                    $order = $transaction->order;
                }
            }

            if (!$order) {
                return redirect('/')->with('error', 'سفارش مورد نظر جهت بازگشت از پرداخت یافت نشد.');
            }

            $success = $this->paymentService->verifyPayment($order, $request);

            if ($success) {
                return to_route('checkout.success', $order->order_number)
                    ->with('success', 'پرداخت و ثبت سفارش شما با موفقیت انجام شد.');
            }

            return to_route('checkout.success', $order->order_number)
                ->with('error', 'پرداخت ناموفق بود. سفارش ثبت شده و می‌توانید مجدداً پرداخت کنید.');
        } catch (Exception $e) {
            if (isset($order) && $order->order_number) {
                return to_route('checkout.success', $order->order_number)->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
            }
            return redirect('/')->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
        }
    }
}
