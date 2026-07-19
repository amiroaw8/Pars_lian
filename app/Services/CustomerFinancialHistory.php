<?php

namespace App\Services;

use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Support\PhoneNumber;
use App\Enums\PaymentStatus;
use Illuminate\Support\Collection;

class CustomerFinancialHistory
{
    public function timeline(Customer $customer): Collection
    {
        $entries = collect();

        $accountedOrderIds = AccountingSale::query()
            ->where('customer_id', $customer->id)
            ->whereNotNull('order_id')
            ->pluck('order_id');

        AccountingSale::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('transaction_date')
            ->get()
            ->each(function (AccountingSale $sale) use ($entries) {
                $entry = [
                    'date' => $sale->created_at ?? $sale->transaction_date,
                    'type' => $this->resolveSaleType($sale),
                    'category' => 'sales',
                    'amount' => (float) $sale->amount,
                    'description' => $sale->description ?: 'ثبت فروش',
                    'reference' => null,
                    'reference_url' => null,
                ];

                if ($sale->order_id) {
                    $entry = array_merge($entry, $this->shopOrderReference((int) $sale->order_id));
                    $entry['order_id'] = (int) $sale->order_id;
                }

                $entries->push($entry);
            });

        AccountingService::query()
            ->whereHas('serviceOrder', fn ($q) => $q->where('customer_id', $customer->id))
            ->with('serviceOrder:id')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (AccountingService $service) => $this->isCustomerLedgerEntry($service))
            ->each(function (AccountingService $service) use ($entries) {
                $entry = [
                    'date' => $service->created_at ?? $service->transaction_date,
                    'type' => $this->resolveServiceType($service),
                    'category' => 'service',
                    'amount' => (float) $service->amount,
                    'description' => $service->description ?: 'ثبت خدمات',
                    'reference' => null,
                    'reference_url' => null,
                ];

                if ($service->service_order_id) {
                    $entry = array_merge($entry, $this->serviceOrderReference((int) $service->service_order_id));
                    $entry['service_order_id'] = (int) $service->service_order_id;
                }

                $entries->push($entry);
            });

        ServiceOrder::query()
            ->where('customer_id', $customer->id)
            ->where('debt_amount', '>', 0)
            ->orderByDesc('updated_at')
            ->get(['id', 'debt_amount', 'debt_reason', 'updated_at', 'created_at'])
            ->each(function (ServiceOrder $order) use ($entries) {
                $ledgerDebt = (float) AccountingService::query()
                    ->where('service_order_id', $order->id)
                    ->where('payment_status', 'unpaid')
                    ->sum('amount');
                $unledgered = max(0, (float) $order->debt_amount - $ledgerDebt);

                if ($unledgered <= 0) {
                    return;
                }

                $entries->push(array_merge([
                    'date' => $order->updated_at ?? $order->created_at,
                    'type' => 'debt',
                    'category' => 'service',
                    'amount' => $unledgered,
                    'description' => $order->debt_reason
                        ? $order->debt_reason . ' (ثبت قبلی)'
                        : 'بدهی ثبت‌شده قبلی',
                    'service_order_id' => (int) $order->id,
                ], $this->serviceOrderReference((int) $order->id)));
            });

        $this->shopOrdersQuery($customer)
            ->when($accountedOrderIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $accountedOrderIds))
            ->where(function ($q) {
                $q->where('payment_method', 'debt')
                    ->orWhere('payment_status', PaymentStatus::PAID);
            })
            ->latest()
            ->get()
            ->each(function (Order $order) use ($entries) {
                $paid = $this->orderIsPaid($order);
                $isDebt = ! $paid && $order->payment_method === 'debt';

                $entries->push(array_merge([
                    'date' => $order->created_at,
                    'type' => $isDebt ? 'debt' : 'payment',
                    'category' => 'sales',
                    'amount' => (float) $order->total,
                    'description' => $isDebt ? 'فروش حضوری — ثبت بدهی' : 'فروش حضوری / فروشگاه',
                    'order_id' => (int) $order->id,
                ], $this->shopOrderReferenceFromModel($order)));
            });

        $this->appendInferredDebts($customer, $entries);
        $this->appendInferredSettledPayments($customer, $entries);

        $result = $entries
            ->sortByDesc(fn ($row) => $row['date'])
            ->values();

        return $result;
    }

    /**
     * Current payment/debt status per service and shop order (live from order records).
     */
    public function orderStatuses(Customer $customer): Collection
    {
        $rows = collect();

        ServiceOrder::query()
            ->where('customer_id', $customer->id)
            ->where(function ($query) {
                $query->where('service_cost', '>', 0)
                    ->orWhere('debt_amount', '>', 0);
            })
            ->orderByDesc('updated_at')
            ->get(['id', 'service_cost', 'debt_amount', 'created_at', 'updated_at'])
            ->each(function (ServiceOrder $order) use ($rows) {
                $total = (float) $order->service_cost;
                $debt = (float) $order->debt_amount;

                if ($debt <= 0 && $total <= 0) {
                    return;
                }

                $rows->push([
                    'category' => 'service',
                    'category_label' => 'خدمات',
                    'reference' => 'سفارش تعمیر #'.$order->id,
                    'reference_url' => route('automation.service-orders.show', $order->id),
                    'amount' => $debt > 0 ? $debt : $total,
                    'total_amount' => $total,
                    'debt_amount' => $debt,
                    'payment_status' => $debt > 0 ? 'debt' : 'paid',
                    'date' => $order->updated_at ?? $order->created_at,
                ]);
            });

        $this->shopOrdersQuery($customer)
            ->where('total', '>', 0)
            ->orderByDesc('updated_at')
            ->get()
            ->each(function (Order $order) use ($rows) {
                $paid = $this->orderIsPaid($order);
                $isDebt = ! $paid && $order->payment_method === 'debt';

                $rows->push([
                    'category' => 'sales',
                    'category_label' => 'فروش',
                    'reference' => $order->order_number ?: ('سفارش #'.$order->id),
                    'reference_url' => route('automation.orders.show', $order->id),
                    'amount' => (float) $order->total,
                    'total_amount' => (float) $order->total,
                    'debt_amount' => $isDebt ? (float) $order->total : 0,
                    'payment_status' => $paid ? 'paid' : ($isDebt ? 'debt' : 'pending'),
                    'date' => $order->updated_at ?? $order->created_at,
                ]);
            });

        return $rows
            ->sortByDesc(fn ($row) => $row['date'])
            ->values();
    }

    /**
     * Ledger of individual payment and debt transactions (excludes proforma).
     */
    public function transactions(Customer $customer): Collection
    {
        return $this->timeline($customer)
            ->whereIn('type', ['payment', 'debt'])
            ->values();
    }

    /**
     * Ensure each payment in the ledger has a matching debt row (for legacy/incomplete records).
     */
    private function appendInferredDebts(Customer $customer, Collection $entries): void
    {
        $this->inferMissingDebtsForOrderGroup(
            $entries,
            'service',
            'service_order_id',
            fn (int $id) => $this->serviceOrderReference($id),
            fn (int $id) => ServiceOrder::query()->find($id)?->created_at
        );

        $this->inferMissingDebtsForOrderGroup(
            $entries,
            'sales',
            'order_id',
            fn (int $id) => $this->shopOrderReference($id),
            fn (int $id) => Order::query()->find($id)?->created_at
        );
    }

    /**
     * @param  callable(int): array{reference: string, reference_url: string}  $referenceResolver
     * @param  callable(int): mixed  $defaultDateResolver
     */
    private function inferMissingDebtsForOrderGroup(
        Collection $entries,
        string $category,
        string $idKey,
        callable $referenceResolver,
        callable $defaultDateResolver
    ): void {
        $paymentsByOrder = $entries
            ->filter(fn ($row) => ($row['category'] ?? '') === $category
                && ($row['type'] ?? '') === 'payment'
                && ! empty($row[$idKey]))
            ->groupBy(fn ($row) => (int) $row[$idKey]);

        foreach ($paymentsByOrder as $orderId => $payments) {
            $debts = $entries->filter(fn ($row) => ($row['category'] ?? '') === $category
                && ($row['type'] ?? '') === 'debt'
                && (int) ($row[$idKey] ?? 0) === (int) $orderId);

            $paidTotal = (float) $payments->sum('amount');
            $debtTotal = (float) $debts->sum('amount');

            if ($paidTotal <= 0 || $debtTotal >= $paidTotal - 0.01) {
                continue;
            }

            $missing = $paidTotal - $debtTotal;
            $paymentDate = $payments->min(fn ($row) => $row['date']);
            $defaultDate = $defaultDateResolver((int) $orderId);

            $entries->push(array_merge([
                'date' => $defaultDate ?? $paymentDate,
                'type' => 'debt',
                'category' => $category,
                'amount' => $missing,
                'description' => $category === 'service'
                    ? '[بدهی] هزینه سفارش تعمیر'
                    : '[بدهی] فروش سفارش',
                $idKey => (int) $orderId,
                'inferred' => true,
            ], $referenceResolver((int) $orderId)));
        }
    }

    /**
     * Backfill payment rows for debts settled before separate payment records existed.
     */
    private function appendInferredSettledPayments(Customer $customer, Collection $entries): void
    {
        AccountingSale::query()
            ->where('customer_id', $customer->id)
            ->whereNotNull('order_id')
            ->where(function ($query) {
                $query->where('description', 'like', '%[بدهی]%')
                    ->orWhere('description', 'like', '% — بدهی%')
                    ->orWhere('payment_method', 'debt');
            })
            ->whereIn('status', ['completed', 'cancelled'])
            ->get()
            ->each(function (AccountingSale $debtSale) use ($customer, $entries) {
                if ($this->hasSalePaymentRecord($customer->id, (int) $debtSale->order_id)) {
                    return;
                }

                $order = Order::query()->find($debtSale->order_id);
                if (! $order || ! $this->orderIsPaid($order)) {
                    return;
                }

                $entries->push(array_merge([
                    'date' => $order->updated_at ?? $debtSale->updated_at ?? $debtSale->transaction_date,
                    'type' => 'payment',
                    'category' => 'sales',
                    'amount' => (float) $debtSale->amount,
                    'description' => '[پرداخت] تسویه بدهی فروش',
                    'order_id' => (int) $order->id,
                ], $this->shopOrderReferenceFromModel($order)));
            });

        $serviceOrderIds = ServiceOrder::query()
            ->where('customer_id', $customer->id)
            ->pluck('id');

        AccountingService::query()
            ->whereIn('service_order_id', $serviceOrderIds)
            ->where('description', 'like', '%[بدهی]%')
            ->where('payment_status', 'paid')
            ->get()
            ->filter(fn (AccountingService $service) => $this->isCustomerLedgerEntry($service))
            ->each(function (AccountingService $debtService) use ($entries) {
                if ($this->hasServicePaymentRecord((int) $debtService->service_order_id)) {
                    return;
                }

                $entries->push(array_merge([
                    'date' => $debtService->updated_at ?? $debtService->transaction_date,
                    'type' => 'payment',
                    'category' => 'service',
                    'amount' => (float) $debtService->amount,
                    'description' => '[پرداخت] تسویه بدهی خدمات',
                    'service_order_id' => (int) $debtService->service_order_id,
                ], $this->serviceOrderReference((int) $debtService->service_order_id)));
            });
    }

    private function hasSalePaymentRecord(int $customerId, int $orderId): bool
    {
        return AccountingSale::query()
            ->where('customer_id', $customerId)
            ->where('order_id', $orderId)
            ->get()
            ->contains(fn (AccountingSale $sale) => $this->resolveSaleType($sale) === 'payment');
    }

    private function hasServicePaymentRecord(int $serviceOrderId): bool
    {
        return AccountingService::query()
            ->where('service_order_id', $serviceOrderId)
            ->get()
            ->contains(fn (AccountingService $service) => $this->resolveServiceType($service) === 'payment');
    }

    private function isCustomerLedgerEntry(AccountingService $service): bool
    {
        $desc = (string) $service->description;

        if (str_contains($desc, 'خودکار بابت') || str_contains($desc, '[حسابداری]')) {
            return false;
        }

        if (str_contains($desc, '[بدهی]') || str_contains($desc, '[پرداخت]') || str_contains($desc, '[پیش‌فاکتور]')) {
            return true;
        }

        if ($service->payment_status === 'unpaid') {
            return true;
        }

        return str_contains($desc, 'تسویه بدهی') || str_contains($desc, 'پرداخت هزینه تعمیر');
    }

    private function orderIsPaid(Order $order): bool
    {
        $status = $order->payment_status;

        if ($status instanceof PaymentStatus) {
            return $status === PaymentStatus::PAID;
        }

        return $status === 'paid';
    }

    public function summary(Customer $customer): array
    {
        $serviceDebt = (float) ServiceOrder::where('customer_id', $customer->id)
            ->where('debt_amount', '>', 0)
            ->sum('debt_amount');

        $accountedOrderIds = AccountingSale::query()
            ->where('customer_id', $customer->id)
            ->whereNotNull('order_id')
            ->pluck('order_id');

        $salesDebt = (float) AccountingSale::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->sum('amount');

        $salesDebt += (float) $this->shopOrdersQuery($customer)
            ->when($accountedOrderIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $accountedOrderIds))
            ->where('payment_method', 'debt')
            ->where('payment_status', PaymentStatus::PENDING)
            ->sum('total');

        $totalPaid = (float) $this->timeline($customer)
            ->where('type', 'payment')
            ->sum('amount');

        return [
            'total_debt' => $serviceDebt + $salesDebt,
            'service_debt' => $serviceDebt,
            'sales_debt' => $salesDebt,
            'total_paid' => $totalPaid,
        ];
    }

    private function shopOrdersQuery(Customer $customer)
    {
        return Order::query()->where(function ($q) use ($customer) {
            PhoneNumber::scopeWherePhoneMatches($q, 'shipping_phone', $customer->phone);
            if ($customer->user_id) {
                $q->orWhere('user_id', $customer->user_id);
            }
        });
    }

    private function resolveSaleType(AccountingSale $sale): string
    {
        $desc = (string) $sale->description;

        if (str_contains($desc, '[پیش‌فاکتور]')) {
            return 'proforma';
        }

        if (str_contains($desc, '[بدهی]') || str_contains($desc, ' — بدهی')) {
            return 'debt';
        }

        if (str_contains($desc, '[پرداخت]')) {
            return 'payment';
        }

        if ($sale->status === 'completed') {
            return 'payment';
        }

        if ($sale->payment_method === 'debt' || $sale->status === 'pending') {
            return 'debt';
        }

        return 'payment';
    }

    private function resolveServiceType(AccountingService $service): string
    {
        $desc = (string) $service->description;

        if (str_contains($desc, '[پیش‌فاکتور]')) {
            return 'proforma';
        }

        if (str_contains($desc, '[بدهی]')) {
            return 'debt';
        }

        if (str_contains($desc, '[پرداخت]') || str_contains($desc, 'تسویه بدهی') || str_contains($desc, 'پرداخت هزینه تعمیر')) {
            return 'payment';
        }

        if ($service->payment_status === 'unpaid') {
            return 'debt';
        }

        return 'payment';
    }

    /** @return array{reference: string, reference_url: string} */
    private function shopOrderReference(int $orderId): array
    {
        $order = Order::query()->find($orderId);

        if ($order) {
            return $this->shopOrderReferenceFromModel($order);
        }

        return [
            'reference' => 'سفارش #'.$orderId,
            'reference_url' => route('automation.orders.show', $orderId),
        ];
    }

    /** @return array{reference: string, reference_url: string} */
    private function shopOrderReferenceFromModel(Order $order): array
    {
        return [
            'reference' => $order->order_number ?: ('سفارش #'.$order->id),
            'reference_url' => route('automation.orders.show', $order->id),
        ];
    }

    /** @return array{reference: string, reference_url: string} */
    private function serviceOrderReference(int $serviceOrderId): array
    {
        return [
            'reference' => 'سفارش تعمیر #'.$serviceOrderId,
            'reference_url' => route('automation.service-orders.show', $serviceOrderId),
        ];
    }
}
