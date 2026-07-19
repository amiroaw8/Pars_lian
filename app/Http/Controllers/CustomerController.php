<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Enums\OrderStatus;
use App\Enums\ServiceOrderStatus;
use App\Services\AccountDeletionService;
use App\Services\CustomerFinancialHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! ($user->canEditServiceOrders() || $user->canAccessPos() || $user->canManageCustomers())) {
            abort(403);
        }

        $term = trim((string) $request->get('q', ''));
        $query = Customer::query()->select('id', 'name', 'phone');

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('id', $term);
            });
        }

        $customers = $query->orderBy('name')->limit(30)->get();

        return response()->json([
            'results' => $customers->map(fn (Customer $c) => [
                'id' => $c->id,
                'text' => $c->name . ' — ' . $c->phone,
            ]),
        ]);
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403, 'شما اجازه دسترسی به مدیریت مشتریان را ندارید.');
        }

        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $minDevices = $request->get('min_devices');

        $query = Customer::withCount(['devices', 'serviceOrders']);

        if ($request->has('trashed')) {
            $query->onlyTrashed();
        }

        $query->when($search, function ($query, $search) {
                $normalized = preg_replace('/\D+/', '', $search);
                $query->where(function ($q) use ($search, $normalized) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                    if ($normalized !== '') {
                        $q->orWhere('phone', 'like', "%{$normalized}%");
                    }
                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($dateFrom, function ($query, $date) {
                $from = \App\Support\JalaliDate::startOfDay($date);
                $query->where('created_at', '>=', $from ?? $date);
            })
            ->when($dateTo, function ($query, $date) {
                $to = \App\Support\JalaliDate::endOfDay($date);
                $query->where('created_at', '<=', $to ?? $date);
            })
            ->when($minDevices, function ($query, $min) {
                $query->has('devices', '>=', $min);
            });

        $customers = $query->latest()->paginate(20)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($customers);
        }

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }
        return view('customers.create');
    }

    public function store(CustomerRequest $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }

        $data = $request->validated();
        $plainPassword = $request->input('password');
        unset($data['in_person'], $data['password']);

        return DB::transaction(function () use ($data, $request, $plainPassword) {
            $accountUser = \App\Models\User::withTrashed()
                ->where('phone', $data['phone'])
                ->first();

            if ($accountUser && $accountUser->isEmployee()) {
                return Redirect::back()->withErrors(['phone' => 'این شماره تلفن به حساب کارمند اختصاص یافته است. لطفاً شماره دیگری وارد کنید.']);
            }

            $existingCustomer = Customer::withTrashed()
                ->where('phone', $data['phone'])
                ->first();

            if ($existingCustomer?->trashed()) {
                $accountUser = $this->resolveCustomerUser($data['phone'], $data['name'], $plainPassword, $accountUser);
                $existingCustomer->restore();
                $existingCustomer->update(array_merge($data, ['user_id' => $accountUser->id]));

                return $this->storeCustomerResponse($request, $existingCustomer->fresh(), restored: true);
            }

            if ($request->boolean('in_person') && $existingCustomer) {
                if (! $accountUser && $existingCustomer->user_id) {
                    $accountUser = \App\Models\User::withTrashed()->find($existingCustomer->user_id);
                }

                $accountUser = $this->resolveCustomerUser($data['phone'], $data['name'], $plainPassword, $accountUser);
                $existingCustomer->update(array_merge($data, ['user_id' => $accountUser->id]));

                return $this->storeCustomerResponse($request, $existingCustomer->fresh(), inPerson: true);
            }

            $accountUser = $this->resolveCustomerUser($data['phone'], $data['name'], $plainPassword, $accountUser);
            $data['user_id'] = $accountUser->id;

            /** @var Customer $customer */
            $customer = Customer::create($data);

            return $this->storeCustomerResponse($request, $customer);
        });
    }

    private function resolveCustomerUser(
        string $phone,
        string $name,
        ?string $plainPassword = null,
        ?\App\Models\User $existingUser = null
    ): \App\Models\User {
        $user = $existingUser ?? \App\Models\User::withTrashed()->where('phone', $phone)->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }

            $updates = ['name' => $name];
            if ($plainPassword) {
                $updates['password'] = Hash::make($plainPassword);
            }
            $user->update($updates);

            if (! $user->hasRole('customer')) {
                $user->assignRole('customer');
            }

            return $user;
        }

        $user = \App\Models\User::create([
            'name' => $name,
            'phone' => $phone,
            'password' => Hash::make($plainPassword ?: $phone),
            'email' => null,
        ]);
        $user->assignRole('customer');

        return $user;
    }

    private function storeCustomerResponse(
        Request $request,
        Customer $customer,
        bool $restored = false,
        bool $inPerson = false
    ) {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
            ]);
        }

        if ($request->has('return_to') && $request->return_to == 'service_orders') {
            session(['new_customer_id' => $customer->id]);

            $message = match (true) {
                $inPerson => 'اطلاعات مشتری حضوری بروزرسانی شد.',
                $restored => 'مشتری قبلی بازیابی و فعال شد.',
                default => 'مشتری با موفقیت ایجاد شد و حساب کاربری مرتبط نیز فعال گردید.',
            };

            return Redirect::route('automation.service-orders.create')->with('success', $message);
        }

        $message = match (true) {
            $inPerson => 'اطلاعات مشتری حضوری بروزرسانی شد.',
            $restored => 'مشتری قبلی بازیابی و فعال شد.',
            default => 'مشتری با موفقیت ایجاد شد و حساب کاربری مرتبط نیز فعال گردید.',
        };

        return Redirect::route('automation.customers.index')->with('success', $message);
    }

    public function show(Customer $customer, Request $request)
    {
        if (!Auth::user()->canManageCustomers()) {
            abort(403);
        }

        $interactionsQuery = $customer->interactions()->with('user')->latest();
        
        if ($request->has('user_id') && $request->user_id) {
            $interactionsQuery->where('user_id', $request->user_id);
        }

        $interactions = $interactionsQuery->get();
        $customer->setRelation('interactions', $interactions);

        $customer->load(['devices', 'serviceOrders.device']);
        
        $customerServiceOrders = \App\Models\ServiceOrder::where('customer_id', $customer->id)
            ->latest()
            ->limit(50)
            ->get(['id', 'status', 'service_cost', 'debt_amount', 'created_at']);
        
        $shopOrdersQuery = \App\Models\Order::query()
            ->where(function ($q) use ($customer) {
                \App\Support\PhoneNumber::scopeWherePhoneMatches($q, 'shipping_phone', $customer->phone);
                if ($customer->user_id) {
                    $q->orWhere('user_id', $customer->user_id);
                }
            });

        $customerShopOrders = (clone $shopOrdersQuery)->latest()->limit(20)->get(['id', 'order_number', 'total', 'payment_status', 'payment_method', 'status', 'created_at', 'notes']);

        $financialSummary = [
            'service_total' => (float) \App\Models\ServiceOrder::where('customer_id', $customer->id)
                ->whereIn('status', ['ready', 'delivered', 'accounting'])
                ->sum('service_cost'),
            'service_debt' => (float) \App\Models\ServiceOrder::where('customer_id', $customer->id)
                ->where('debt_amount', '>', 0)
                ->sum('debt_amount'),
            'sales_total' => (float) \App\Models\AccountingSale::where('customer_id', $customer->id)->sum('amount'),
            'sales_debt' => (float) \App\Models\AccountingSale::where('customer_id', $customer->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'shop_orders' => (clone $shopOrdersQuery)->count(),
        ];
        
        // Get all users who have interacted with this customer for the filter
        $interactionUsers = \App\Models\CustomerInteraction::where('customer_id', $customer->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->filter();

        $financialHistory = app(CustomerFinancialHistory::class)->transactions($customer);
        $orderPaymentStatuses = app(CustomerFinancialHistory::class)->orderStatuses($customer);
        $debtSummary = app(CustomerFinancialHistory::class)->summary($customer);


        return view('customers.show', compact(
            'customer',
            'interactionUsers',
            'financialSummary',
            'financialHistory',
            'orderPaymentStatuses',
            'debtSummary',
            'customerServiceOrders',
            'customerShopOrders'
        ));
    }

    public function storeInteraction(Request $request, Customer $customer)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:call,meeting,note,email,sms',
            'content' => 'required|string',
            'interaction_date' => 'required|date',
        ]);

        $customer->interactions()->create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'content' => $validated['content'],
            'interaction_date' => $validated['interaction_date'],
        ]);

        return Redirect::back()->with('success', 'تعامل با مشتری با موفقیت ثبت شد.');
    }

    public function storeFinancialTransaction(Request $request, Customer $customer)
    {
        $user = Auth::user();
        if (! $user || ! ($user->canManageAccounting() || $user->canManageCustomers() || $user->isAdmin())) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => 'required|in:service,sales',
            'record_type' => 'required|in:payment,debt',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'service_order_id' => 'nullable|exists:service_orders,id',
            'order_id' => 'nullable|exists:orders,id',
            'confirm' => 'accepted',
        ], [
            'confirm.accepted' => 'برای ثبت باید تایید کنید که اطلاعات صحیح است.',
            'amount.min' => 'مبلغ باید بیشتر از صفر باشد.',
        ]);

        if ($validated['record_type'] === 'debt' && $validated['category'] === 'service' && empty($validated['service_order_id'])) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['service_order_id' => 'برای ثبت بدهی خدمات، انتخاب سفارش تعمیر الزامی است.']);
        }

        if ($validated['category'] === 'sales') {
            $validated['service_order_id'] = null;
        } else {
            $validated['order_id'] = null;
        }

        $amount = (float) $validated['amount'];
        $desc = $validated['description'] ?? '';
        $prefix = match ($validated['record_type']) {
            'payment' => 'پرداخت',
            'debt' => 'بدهی',
        };
        $fullDesc = trim("[{$prefix}] {$desc}") ?: $prefix;

        if ($validated['category'] === 'sales') {
            if ($validated['record_type'] === 'payment') {
                if (! empty($validated['order_id'])) {
                    $this->recordShopOrderPayment(
                        $customer,
                        $amount,
                        $fullDesc,
                        (int) $validated['order_id']
                    );
                } else {
                    $remaining = $amount;
                    $pendingSales = \App\Models\AccountingSale::where('customer_id', $customer->id)
                        ->where('status', 'pending')
                        ->orderBy('id')
                        ->get();

                    foreach ($pendingSales as $sale) {
                        if ($remaining <= 0) {
                            break;
                        }
                        if ((float) $sale->amount <= $remaining) {
                            $saleAmount = (float) $sale->amount;
                            $remaining -= $saleAmount;
                            $sale->update(['status' => 'cancelled']);

                            \App\Models\AccountingSale::create([
                                'customer_id' => $customer->id,
                                'order_id' => $sale->order_id,
                                'amount' => $saleAmount,
                                'description' => $fullDesc,
                                'transaction_date' => now(),
                                'payment_method' => 'cash',
                                'status' => 'completed',
                            ]);

                            if ($sale->order_id) {
                                \App\Models\Order::whereKey($sale->order_id)->update([
                                    'payment_status' => \App\Enums\PaymentStatus::PAID,
                                ]);
                            }
                        }
                    }

                    if ($remaining > 0) {
                        \App\Models\AccountingSale::create([
                            'customer_id' => $customer->id,
                            'amount' => $remaining,
                            'description' => $fullDesc,
                            'transaction_date' => now(),
                            'payment_method' => 'cash',
                            'status' => 'completed',
                        ]);
                    }
                }
            } else {
                $orderId = ! empty($validated['order_id']) ? (int) $validated['order_id'] : null;
                if ($orderId) {
                    $this->findCustomerShopOrder($customer, $orderId);
                }

                \App\Models\AccountingSale::create([
                    'customer_id' => $customer->id,
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'description' => $fullDesc,
                    'transaction_date' => now(),
                    'payment_method' => 'debt',
                    'status' => 'pending',
                ]);

                if ($orderId) {
                    \App\Models\Order::whereKey($orderId)->update([
                        'payment_method' => 'debt',
                        'payment_status' => \App\Enums\PaymentStatus::PENDING,
                    ]);
                }
            }
        } else {
            if ($validated['record_type'] === 'payment') {
                if (! empty($validated['service_order_id'])) {
                    $order = \App\Models\ServiceOrder::where('id', $validated['service_order_id'])
                        ->where('customer_id', $customer->id)
                        ->firstOrFail();
                    $this->recordServicePayment($order, $amount, $fullDesc);
                } else {
                    $paymentAmount = $amount;
                    $ordersWithDebt = \App\Models\ServiceOrder::where('customer_id', $customer->id)
                        ->where('debt_amount', '>', 0)
                        ->orderBy('id')
                        ->get();

                    foreach ($ordersWithDebt as $order) {
                        if ($paymentAmount <= 0) {
                            break;
                        }
                        $applied = $this->recordServicePayment($order, $paymentAmount, $fullDesc);
                        $paymentAmount -= $applied;
                    }

                    if ($paymentAmount > 0) {
                        \App\Models\AccountingService::create([
                            'service_order_id' => null,
                            'amount' => $paymentAmount,
                            'description' => $fullDesc,
                            'transaction_date' => now(),
                            'payment_status' => 'paid',
                        ]);
                    }
                }
            } elseif ($validated['record_type'] === 'debt' && ! empty($validated['service_order_id'])) {
                $order = \App\Models\ServiceOrder::where('id', $validated['service_order_id'])
                    ->where('customer_id', $customer->id)
                    ->firstOrFail();
                $order->update([
                    'debt_amount' => (float) ($order->debt_amount ?? 0) + $amount,
                    'debt_reason' => $fullDesc,
                ]);
                \App\Models\AccountingService::create([
                    'service_order_id' => $order->id,
                    'technician_id' => $order->technician_id,
                    'amount' => $amount,
                    'description' => $fullDesc,
                    'transaction_date' => now(),
                    'payment_status' => 'unpaid',
                ]);
            } elseif ($validated['record_type'] !== 'payment') {
                $paymentStatus = $validated['record_type'] === 'payment' ? 'paid' : 'unpaid';
                \App\Models\AccountingService::create([
                    'service_order_id' => $validated['service_order_id'] ?? null,
                    'amount' => $amount,
                    'description' => $fullDesc,
                    'transaction_date' => now(),
                    'payment_status' => $paymentStatus,
                ]);
            }
        }

        return Redirect::back()->with('success', 'تراکنش مالی با موفقیت ثبت شد.');
    }

    /**
     * Reduce service order debt by payment amount. Returns remaining unapplied payment.
     */
    private function applyServiceOrderPayment(\App\Models\ServiceOrder $order, float $amount): float
    {
        $debt = (float) ($order->debt_amount ?? 0);
        if ($debt <= 0 || $amount <= 0) {
            return $amount;
        }

        $applied = min($amount, $debt);
        $order->update([
            'debt_amount' => max(0, $debt - $applied),
        ]);

        return $amount - $applied;
    }

    /**
     * Settle service-order payment against unpaid ledger rows and remaining debt.
     * Returns the amount actually applied.
     */
    private function recordServicePayment(\App\Models\ServiceOrder $order, float $amount, string $description): float
    {
        if ($amount <= 0) {
            return 0.0;
        }

        $this->ensureServiceDebtRecord($order, $amount);

        $remaining = $amount;
        $settledFromLedger = 0.0;

        $unpaidEntries = \App\Models\AccountingService::query()
            ->where('service_order_id', $order->id)
            ->where('payment_status', 'unpaid')
            ->orderBy('id')
            ->get();

        foreach ($unpaidEntries as $entry) {
            if ($remaining <= 0) {
                break;
            }

            $entryAmount = (float) $entry->amount;
            if ($entryAmount <= $remaining + 0.01) {
                $entry->update(['payment_status' => 'paid']);
                $settledFromLedger += $entryAmount;
                $remaining -= $entryAmount;
            }
        }

        if ($settledFromLedger > 0) {
            $this->applyServiceOrderPayment($order, $settledFromLedger);
        }

        if ($remaining > 0) {
            $remaining = $this->applyServiceOrderPayment($order, $remaining);
        }

        \App\Models\AccountingService::create([
            'service_order_id' => $order->id,
            'technician_id' => $order->technician_id,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => now(),
            'payment_status' => 'paid',
        ]);

        return $amount - $remaining;
    }

    private function ensureServiceDebtRecord(\App\Models\ServiceOrder $order, float $amount): void
    {
        $hasDebtRecord = \App\Models\AccountingService::query()
            ->where('service_order_id', $order->id)
            ->where(function ($query) {
                $query->where('description', 'like', '%[بدهی]%')
                    ->orWhere('payment_status', 'unpaid');
            })
            ->exists();

        if ($hasDebtRecord) {
            return;
        }

        \App\Models\AccountingService::create([
            'service_order_id' => $order->id,
            'technician_id' => $order->technician_id,
            'amount' => $amount,
            'description' => '[بدهی] '.($order->debt_reason ?: 'بدهی سفارش تعمیر'),
            'transaction_date' => now(),
            'payment_status' => 'unpaid',
        ]);
    }

    private function findCustomerShopOrder(Customer $customer, int $orderId): \App\Models\Order
    {
        return \App\Models\Order::query()
            ->whereKey($orderId)
            ->where(function ($q) use ($customer) {
                \App\Support\PhoneNumber::scopeWherePhoneMatches($q, 'shipping_phone', $customer->phone);
                if ($customer->user_id) {
                    $q->orWhere('user_id', $customer->user_id);
                }
            })
            ->firstOrFail();
    }

    private function recordShopOrderPayment(Customer $customer, float $amount, string $description, int $orderId): void
    {
        $order = $this->findCustomerShopOrder($customer, $orderId);

        $hasDebtRecord = \App\Models\AccountingSale::query()
            ->where('order_id', $order->id)
            ->where(function ($query) {
                $query->where('description', 'like', '%[بدهی]%')
                    ->orWhere('description', 'like', '% — بدهی%')
                    ->orWhere('payment_method', 'debt')
                    ->orWhere('status', 'pending');
            })
            ->exists();

        if (! $hasDebtRecord) {
            \App\Models\AccountingSale::create([
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'amount' => $amount,
                'description' => '[بدهی] فروش سفارش',
                'transaction_date' => now(),
                'payment_method' => 'debt',
                'status' => 'pending',
            ]);
        }

        \App\Models\AccountingSale::query()
            ->where('order_id', $order->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        \App\Models\AccountingSale::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => now(),
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $order->update(['payment_status' => \App\Enums\PaymentStatus::PAID]);
    }

    public function edit(Customer $customer)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->canManageCustomers()) {
            abort(403);
        }
        
        $data = $request->validated();
        
        return DB::transaction(function () use ($data, $customer, $currentUser) {
            // Handle password change if provided and user has permission
            $passwordUpdateData = [];
            if ($currentUser->canManageCustomers() && ! empty($data['password'])) {
                $passwordUpdateData = [
                    'password' => Hash::make($data['password']),
                ];
            }

            unset($data['password'], $data['password_confirmation']);

            $linkedUser = $customer->user_id ? \App\Models\User::find($customer->user_id) : null;

            // Check if user exists for the (possibly new) phone
            $user = \App\Models\User::where('phone', $data['phone'])->first();
            if ($user && $user->isEmployee()) {
                return Redirect::back()->withErrors(['phone' => 'این شماره تلفن به حساب کارمند اختصاص یافته است. لطفاً شماره دیگری وارد کنید.']);
            }
            if (!$user) {
                // If customer was linked to a user, update that user's phone?
                // Or create a new user? 
                // Better approach: If linked user exists, update their phone. If not, create new.
                if ($customer->user_id) {
                    $user = \App\Models\User::find($customer->user_id);
                    if ($user) {
                        $updateData = [
                            'phone' => $data['phone'],
                            'name' => $data['name'],
                        ];
                        $user->update([
                            'phone' => $data['phone'],
                            'name' => $data['name'],
                        ]);
                    }
                } else {
                    // Create new User
                    /** @var \App\Models\User $user */
                    $user = \App\Models\User::create([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'password' => Hash::make($data['phone']),
                    ]);
                    if ($user) {
                        $user->assignRole('customer');
                    }
                }
            } else {
                // User exists with this phone.
                // Update user name to match customer name if needed
                $updateData = [];
                if ($user->name !== $data['name']) {
                    $updateData['name'] = $data['name'];
                }
                if (! empty($updateData)) {
                    $user->update($updateData);
                }
            }

            if (! $user && $customer->user_id) {
                $user = \App\Models\User::find($customer->user_id);
            }

            if (! empty($passwordUpdateData)) {
                $passwordUser = $user ?? $linkedUser ?? ($customer->user_id ? \App\Models\User::find($customer->user_id) : null);
                if ($passwordUser && ! $passwordUser->isEmployee()) {
                    $passwordUser->update($passwordUpdateData);
                }
            }

            $data['user_id'] = $user ? $user->id : null;

            $customer->update($data);

            $message = 'اطلاعات مشتری و حساب کاربری مرتبط با موفقیت بروزرسانی شد.';
            if (!empty($passwordUpdateData)) {
                $message .= ' رمز ورود نیز تغییر یافت.';
            }

            return Redirect::route('automation.customers.index')
                ->with('success', $message);
        });
    }

    public function destroy(Customer $customer)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }

        app(AccountDeletionService::class)->softDeleteCustomer($customer);

        return Redirect::route('automation.customers.index')
            ->with('success', 'مشتری و تمام سوابق مرتبط (سفارش‌ها، پرداخت‌ها، فایل‌ها) به سطل زباله منتقل شد.');
    }

    public function restore($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }

        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();

        return Redirect::route('automation.customers.index', ['trashed' => 1])
            ->with('success', 'مشتری با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->canManageCustomers()) {
            abort(403);
        }

        $customer = Customer::withTrashed()->findOrFail($id);

        app(AccountDeletionService::class)->forceDeleteCustomer($customer);

        return Redirect::route('automation.customers.index', ['trashed' => 1])
            ->with('success', 'مشتری و تمام سوابق مرتبط برای همیشه حذف شد.');
    }
}
