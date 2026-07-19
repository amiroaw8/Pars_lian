<?php

namespace App\Services;

use App\Models\AccountingSale;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceOrder;
use Illuminate\Support\Collection;

class CustomerFinancialInsights
{
    public function debtors(int $limit = 15): Collection
    {
        return Customer::query()
            ->get()
            ->map(function (Customer $customer) {
                $serviceDebt = (float) ServiceOrder::where('customer_id', $customer->id)
                    ->where('debt_amount', '>', 0)
                    ->sum('debt_amount');
                $salesDebt = (float) AccountingSale::where('customer_id', $customer->id)
                    ->where('status', 'pending')
                    ->sum('amount');
                $totalDebt = $serviceDebt + $salesDebt;

                return [
                    'customer' => $customer,
                    'service_debt' => $serviceDebt,
                    'sales_debt' => $salesDebt,
                    'total_debt' => $totalDebt,
                ];
            })
            ->filter(fn ($row) => $row['total_debt'] > 0)
            ->sortByDesc('total_debt')
            ->take($limit)
            ->values();
    }

    public function goodPayers(int $limit = 15): Collection
    {
        return Customer::query()
            ->get()
            ->map(function (Customer $customer) {
                $paidServices = (float) ServiceOrder::where('customer_id', $customer->id)
                    ->whereIn('status', ['ready', 'delivered'])
                    ->where(function ($q) {
                        $q->whereNull('debt_amount')->orWhere('debt_amount', '<=', 0);
                    })
                    ->sum('service_cost');
                $paidSales = (float) AccountingSale::where('customer_id', $customer->id)
                    ->where('status', 'completed')
                    ->sum('amount');
                $shopPaid = (float) Order::where('shipping_phone', $customer->phone)
                    ->where('payment_status', 'paid')
                    ->sum('total');
                $totalPaid = $paidServices + $paidSales + $shopPaid;
                $debt = $this->customerDebt($customer);

                return [
                    'customer' => $customer,
                    'total_paid' => $totalPaid,
                    'debt' => $debt,
                ];
            })
            ->filter(fn ($row) => $row['total_paid'] > 0 && $row['debt'] <= 0)
            ->sortByDesc('total_paid')
            ->take($limit)
            ->values();
    }

    public function valuable(int $limit = 15): Collection
    {
        return Customer::query()
            ->get()
            ->map(function (Customer $customer) {
                $serviceTotal = (float) ServiceOrder::where('customer_id', $customer->id)
                    ->sum('service_cost');
                $salesTotal = (float) AccountingSale::where('customer_id', $customer->id)->sum('amount');
                $shopTotal = (float) Order::where('shipping_phone', $customer->phone)->sum('total');
                $lifetime = $serviceTotal + $salesTotal + $shopTotal;

                return [
                    'customer' => $customer,
                    'lifetime_value' => $lifetime,
                    'service_total' => $serviceTotal,
                    'sales_total' => $salesTotal,
                    'shop_total' => $shopTotal,
                ];
            })
            ->filter(fn ($row) => $row['lifetime_value'] > 0)
            ->sortByDesc('lifetime_value')
            ->take($limit)
            ->values();
    }

    private function customerDebt(Customer $customer): float
    {
        $serviceDebt = (float) ServiceOrder::where('customer_id', $customer->id)
            ->where('debt_amount', '>', 0)
            ->sum('debt_amount');
        $salesDebt = (float) AccountingSale::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->sum('amount');

        return $serviceDebt + $salesDebt;
    }
}
