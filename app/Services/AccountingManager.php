<?php

namespace App\Services;

use App\Models\AccountingSale;
use App\Models\AccountingService;
use Illuminate\Support\Facades\Cache;

readonly class AccountingManager
{
    public function recordSale(float|string $amount, string $description, ?int $customerId = null, ?int $orderId = null, ?string $transactionDate = null, string $paymentMethod = 'cash', string $status = 'completed'): AccountingSale
    {
        $sale = new AccountingSale([
            'description' => $description,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'transaction_date' => $transactionDate ?? now(),
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'status' => $status,
        ]);
        $sale->save();

        Cache::forget('accounting_totals');
        Cache::forget('accounting_totals_v6');

        return $sale;
    }

    public function recordService(float|string $amount, string $description, int $serviceOrderId, ?int $technicianId = null, ?string $transactionDate = null, float|string $taxAmount = 0): AccountingService
    {
        $service = new AccountingService([
            'description' => $description,
            'service_order_id' => $serviceOrderId,
            'technician_id' => $technicianId,
            'transaction_date' => $transactionDate ?? now(),
            'payment_status' => 'paid',
            'tax_amount' => $taxAmount,
        ]);
        $service->forceFill([
            'amount' => $amount,
        ]);
        $service->save();

        Cache::forget('accounting_totals');
        Cache::forget('accounting_totals_v6');

        return $service;
    }

    public function getMonthlyReport(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? (int) date('Y');
        $month = $month ?? (int) date('m');

        $sales = AccountingSale::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        $services = AccountingService::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->sum('amount');

        return [
            'sales' => $sales,
            'services' => $services,
            'total' => $sales + $services,
            'year' => $year,
            'month' => $month,
        ];
    }

    public function getCustomerBalance($customerId)
    {
        return AccountingSale::where('customer_id', $customerId)
            ->sum('amount');
    }

    public function getTechnicianEarnings($technicianId, $year = null, $month = null)
    {
        $query = AccountingService::where('technician_id', $technicianId);

        if ($year) {
            $query->whereYear('transaction_date', $year);
        }

        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        return $query->sum('amount');
    }
}
