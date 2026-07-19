<?php

namespace App\Http\Controllers\Automation;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Services\AccountingManager;
use App\Support\PhoneNumber;
use App\Support\OrderShippingDefaults;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class POSController extends Controller
{
    public function __construct(
        private readonly AccountingManager $accountingManager
    ) {
        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->canAccessPos()) {
                abort(403, 'شما اجازه دسترسی به فروش حضوری را ندارید.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $products = Product::active()->where('manage_stock', true)->where('stock_quantity', '>', 0)->get();
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);

        return view('automation.pos.index', compact('products', 'customers'));
    }

    public function sales(Request $request)
    {
        $baseQuery = Order::query()->posSales();

        $statsQuery = clone $baseQuery;
        $todayQuery = (clone $baseQuery)->whereDate('created_at', today());

        $stats = [
            'total_count' => (clone $statsQuery)->count(),
            'total_amount' => (int) (clone $statsQuery)->sum('total'),
            'today_count' => (clone $todayQuery)->count(),
            'today_amount' => (int) (clone $todayQuery)->sum('total'),
            'debt_count' => (clone $statsQuery)->where('payment_method', 'debt')->count(),
        ];

        $orders = (clone $baseQuery)
            ->with(['user', 'items'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('shipping_first_name', 'like', "%{$search}%")
                        ->orWhere('shipping_last_name', 'like', "%{$search}%")
                        ->orWhere('shipping_phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->payment_method))
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('automation.pos.sales', compact('orders', 'stats'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        $products = Product::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->where('manage_stock', true)
            ->where('stock_quantity', '>', 0)
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cod,card,online,debt,cash',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $customer = Customer::find($request->customer_id);
                $user = Auth::user();
                $orderUserId = $this->resolvePosOrderUserId($customer);
                $paymentMethod = $request->payment_method === 'cash' ? 'cod' : $request->payment_method;
                $isDebt = $paymentMethod === 'debt';

                // Create Order
                $order = Order::create(array_merge(OrderShippingDefaults::fromCustomer($customer), [
                    'user_id' => $orderUserId,
                    'status' => OrderStatus::DELIVERED,
                    'payment_status' => $isDebt ? PaymentStatus::PENDING : PaymentStatus::PAID,
                    'payment_method' => $isDebt ? 'debt' : $paymentMethod,
                    'shipping_method' => 'pickup',
                    'subtotal' => 0,
                    'total' => 0,
                    'shipping_amount' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'currency' => 'IRT',
                    'notes' => 'ثبت شده از طریق پنل فروش حضوری (POS)',
                    'delivered_at' => now(),
                ]));

                $subtotal = 0;

                foreach ($request->items as $itemData) {
                    $product = Product::lockForUpdate()->findOrFail($itemData['id']);

                    if ($product->inventory_id) {
                        \App\Models\Inventory::where('id', $product->inventory_id)->lockForUpdate()->first();
                    }

                    if (!$product->canBeOrdered($itemData['quantity'])) {
                        throw new \Exception("موجودی محصول {$product->name} کافی نیست.");
                    }

                    $orderItem = new OrderItem([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $itemData['quantity'],
                        'price' => \App\Support\ShopFormat::toIntegerAmount($product->current_price),
                    ]);
                    $order->items()->save($orderItem);

                    // Reduce stock
                    $product->reduceStock($itemData['quantity'], 'shop_pos', $order->id);

                    $subtotal += ($product->current_price * $itemData['quantity']);
                }

                $order->subtotal = (int) round($subtotal);
                $order->tax_amount = 0;
                $order->total = (int) round($subtotal);
                $order->save();

                // Record Accounting Sale
                $this->accountingManager->recordSale(
                    $order->total,
                    "فروش حضوری (سفارش #{$order->order_number})" . ($isDebt ? ' — بدهی' : ''),
                    $customer->id,
                    $order->id,
                    now()->format('Y-m-d'),
                    $isDebt ? 'debt' : $paymentMethod,
                    $isDebt ? 'pending' : 'completed'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'فروش با موفقیت ثبت شد.',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'redirect_url' => route('automation.orders.show', $order),
                ], 201);
            });
        } catch (\Throwable $e) {
            Log::error('POS checkout failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'خطایی در ثبت فروش رخ داد.'
            ], 422);
        }
    }

    private function resolvePosOrderUserId(Customer $customer): int
    {
        if ($customer->user_id) {
            return (int) $customer->user_id;
        }

        $tail = PhoneNumber::tail10($customer->phone);
        if ($tail !== '') {
            $matchedUser = User::whereRaw(
                "REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '+', '') LIKE ?",
                ["%{$tail}%"]
            )->first();

            if ($matchedUser) {
                $customer->forceFill(['user_id' => $matchedUser->id])->saveQuietly();

                return (int) $matchedUser->id;
            }
        }

        $user = User::create([
            'name' => $customer->name,
            'phone' => $customer->phone,
            'password' => Str::password(32),
            'is_active' => true,
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('customer');
        }

        $customer->forceFill(['user_id' => $user->id])->saveQuietly();

        return (int) $user->id;
    }
}
