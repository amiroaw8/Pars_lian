<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\CheckoutRequest;

class CheckoutController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Cart|null $cart */
        $cart = Cart::getCurrentCart();
        $cart?->load('items.product');

        if (!$cart) {
            return Redirect::route('cart.index')->with('error', 'سبد خرید یافت نشد.');
        }

        if ($cart->items->isEmpty()) {
            return Redirect::route('cart.index')->with('error', 'سبد خرید شما خالی است.');
        }

        // Check availability
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }
            if (!$product->canBeOrdered($item->quantity)) {
                return Redirect::route('cart.index')->with('error', "محصول {$product->name} موجود نیست.");
            }
        }

        return view('shop.checkout', compact('cart'));
    }

    public function store(CheckoutRequest $request)
    {
        /** @var \App\Models\Cart|null $cart */
        $cart = Cart::getCurrentCart();
        $cart?->load('items.product');

        if (!$cart) {
            return Redirect::route('cart.index')->with('error', 'سبد خرید یافت نشد.');
        }

        if ($cart->items->isEmpty()) {
            return Redirect::route('cart.index')->with('error', 'سبد خرید شما خالی است.');
        }

        // Check availability
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }
            if (!$product->canBeOrdered($item->quantity)) {
                return Redirect::back()->with('error', "محصول {$product->name} موجود نیست.");
            }
        }

        $order = DB::transaction(function () use ($request, $cart) {
            try {
                // Re-check and lock availability inside transaction
                foreach ($cart->items as $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                    if (!$product || !$product->canBeOrdered($item->quantity)) {
                        $productName = $product ? $product->name : 'ناشناس';
                        throw new \Exception("متاسفانه محصول {$productName} در لحظه ثبت سفارش ناموجود شد یا موجودی آن کافی نیست.");
                    }
                }

                $newOrder = $cart->convertToOrder([
                    'user_id' => Auth::id(),
                    'status' => OrderStatus::PENDING,
                    'payment_status' => PaymentStatus::PENDING,
                    'payment_method' => $request->payment_method,
                    'currency' => config('shop.currency', 'IRT'),
                    'notes' => $request->notes,
                    'shipping_first_name' => $request->shipping_first_name,
                    'shipping_last_name' => $request->shipping_last_name,
                    'shipping_email' => $request->shipping_email,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_postal_code' => $request->shipping_postal_code,
                    'shipping_method' => $request->shipping_method,
                    'shipping_country' => 'Iran',
                ]);

                // Update user profile if fields are empty
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                if ($user) {
                    $profileUpdated = false;

                    if (!$user->first_name) {
                        $user->first_name = $request->shipping_first_name;
                        $profileUpdated = true;
                    }
                    if (!$user->last_name) {
                        $user->last_name = $request->shipping_last_name;
                        $profileUpdated = true;
                    }
                    if (!$user->province) {
                        $user->province = $request->shipping_state;
                        $profileUpdated = true;
                    }
                    if (!$user->city) {
                        $user->city = $request->shipping_city;
                        $profileUpdated = true;
                    }
                    if (!$user->postal_code) {
                        $user->postal_code = $request->shipping_postal_code;
                        $profileUpdated = true;
                    }

                    if ($profileUpdated) {
                        $user->name = trim($user->first_name . ' ' . $user->last_name);
                        $user->save();
                    }

                    // Sync or Create Customer Record
                    $customerData = [
                        'name' => $user->name ?: trim($request->shipping_first_name . ' ' . $request->shipping_last_name),
                        'phone' => $user->phone ?: $request->shipping_phone,
                        'address' => $request->shipping_address . ' - ' . $request->shipping_city . ' - ' . $request->shipping_state,
                        'user_id' => $user->id,
                    ];

                    $customer = \App\Models\Customer::where('user_id', $user->id)->first();

                    if ($customer) {
                        // Update existing customer if linked to user or found by phone
                        $customer->update([
                            'user_id' => $user->id, // Ensure user_id is set
                            // Optionally update address if empty
                            'address' => $customer->address ?: $customerData['address'],
                        ]);
                    } else {
                        // Create new customer
                        \App\Models\Customer::create($customerData);
                    }
                }

                if (!$newOrder || !$newOrder->order_number) {
                    throw new \Exception('شماره سفارش ایجاد نشد.');
                }

                return $newOrder;

            } catch (\Exception $e) {
                Log::error('Order creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => Auth::id(),
                ]);

                throw $e;
            }
        });

        if (!$order) {
            return Redirect::back()->with('error', 'خطا در ثبت سفارش. لطفا دوباره تلاش کنید.');
        }

        Cache::forget('dashboard_stats');
        event(new \App\Events\OrderCreated($order));

        if ($order->payment_method === 'online') {
            return Redirect::route('payment.pay', $order);
        }

        session(['last_completed_order' => $order->order_number]);

        return Redirect::route('checkout.success', $order->order_number);
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if (Auth::id() && (int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        session()->forget('last_completed_order');

        return view('shop.checkout-success', compact('order'));
    }

}
