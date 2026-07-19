<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Enums\ServiceOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Morilog\Jalali\Jalalian;

class AnalyticsService
{
    public function getServiceOrderStats(): array
    {
        return Cache::remember('service_order_stats', config('settings.cache_ttl', 300), function () {
            return [
                'total' => ServiceOrder::count(),
                'registered' => ServiceOrder::where('status', 'registered')->count(),
                'repairing' => ServiceOrder::where('status', 'repairing')->count(),
                'ready' => ServiceOrder::where('status', 'ready')->count(),
                'delivered' => ServiceOrder::where('status', 'delivered')->count(),
                'technician_assigned' => ServiceOrder::where('status', 'technician_assigned')->count(),
                'pending_parts' => ServiceOrder::where('status', 'pending_parts')->count(),
                'accounting' => ServiceOrder::where('status', 'accounting')->count(),
                'rejected' => ServiceOrder::where('status', 'rejected')->count(),
                'archived' => ServiceOrder::where('status', 'archived')->count(),
                'sent_to_workshop' => ServiceOrder::where('status', 'sent_to_workshop')->count(),
                'avg_repair_time' => $this->getAverageRepairTime(),
                'revenue' => ServiceOrder::sum('service_cost'),
            ];
        });
    }

    public function getInventoryStats(): array
    {
        return Cache::remember('inventory_stats', config('settings.inventory_cache_ttl', 600), function () {
            return [
                'total_items' => Inventory::count(),
                'low_stock' => Inventory::where('quantity', '<', config('settings.low_stock_threshold', 5))->count(),
                'out_of_stock' => Inventory::where('quantity', 0)->count(),
                'total_value' => Inventory::sum(DB::raw('quantity * price')),
            ];
        });
    }

    public function getCustomerStats(): array
    {
        return Cache::remember('customer_stats', config('settings.customer_cache_ttl', 3600), function () {
            return [
                'total_customers' => Customer::count(),
                'active_customers' => Customer::has('serviceOrders')->count(),
                'new_customers_this_month' => Customer::whereMonth('created_at', now()->month)->count(),
            ];
        });
    }

    public function getAverageRepairTime(): float
    {
        $driver = DB::connection()->getDriverName();
        $timeDiffSql = $driver === 'sqlite'
            ? 'AVG((julianday(repair_completed_at) - julianday(repair_started_at)) * 24)'
            : 'AVG(TIMESTAMPDIFF(HOUR, repair_started_at, repair_completed_at))';

        return ServiceOrder::whereNotNull('repair_completed_at')
            ->selectRaw("$timeDiffSql as avg_hours")
            ->value('avg_hours') ?? 0;
    }

    public function getMonthlyRevenue(): array
    {
        return Cache::remember('monthly_revenue_' . now()->format('Y-m'), config('settings.revenue_cache_ttl', 86400), function () {
            $driver = DB::connection()->getDriverName();
            $monthRepairs = $driver === 'sqlite' ? "CAST(strftime('%m', created_at) AS INTEGER)" : 'MONTH(created_at)';
            $monthSales = $driver === 'sqlite' ? "CAST(strftime('%m', transaction_date) AS INTEGER)" : 'MONTH(transaction_date)';

            $repairs = ServiceOrder::whereYear('created_at', now()->year)
                ->selectRaw("$monthRepairs as month, SUM(service_cost) as revenue")
                ->groupBy('month')
                ->pluck('revenue', 'month')
                ->toArray();

            $sales = \App\Models\AccountingSale::whereYear('transaction_date', now()->year)
                ->selectRaw("$monthSales as month, SUM(amount) as revenue")
                ->groupBy('month')
                ->pluck('revenue', 'month')
                ->toArray();

            $combined = [];
            for ($i = 1; $i <= 12; $i++) {
                $combined[$i] = ($repairs[$i] ?? 0) + ($sales[$i] ?? 0);
            }
            return $combined;
        });
    }

    public function getCurrentJalaliMonthRevenue(): int
    {
        $now = Jalalian::now();
        $cacheKey = 'current_jalali_month_revenue_'.$now->format('Y-m');

        return (int) Cache::remember($cacheKey, config('settings.revenue_cache_ttl', 3600), function () use ($now) {
            [$start, $end] = $this->jalaliMonthRange($now->getYear(), $now->getMonth());

            $sales = (int) AccountingSale::query()
                ->where(function ($query) {
                    $query->where('status', 'completed')->orWhereNull('status');
                })
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $services = (int) AccountingService::query()
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            return $sales + $services;
        });
    }

    /** @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon} */
    private function jalaliMonthRange(int $year, int $month): array
    {
        $start = Jalalian::fromFormat('Y/n/j', sprintf('%d/%d/1', $year, $month))->toCarbon()->startOfDay();
        $daysInMonth = Jalalian::fromFormat('Y/n/j', sprintf('%d/%d/1', $year, $month))->getMonthDays();
        $end = Jalalian::fromFormat('Y/n/j', sprintf('%d/%d/%d', $year, $month, $daysInMonth))->toCarbon()->endOfDay();

        return [$start, $end];
    }

    public function getAdvancedAnalytics(): array
    {
        return Cache::remember('advanced_analytics', config('settings.cache_ttl', 300), function () {
            return [
                'technician_performance' => $this->getTechnicianPerformance(),
                'top_selling_products' => $this->getTopSellingProducts(),
                'device_type_distribution' => $this->getDeviceTypeDistribution(),
                'customer_retention_rate' => $this->getCustomerRetentionRate(),
            ];
        });
    }

    private function getTechnicianPerformance(): array
    {
        $driver = DB::connection()->getDriverName();
        $timeDiffSql = $driver === 'sqlite'
            ? 'AVG((julianday(repair_completed_at) - julianday(repair_started_at)) * 24)'
            : 'AVG(TIMESTAMPDIFF(HOUR, repair_started_at, repair_completed_at))';

        return DB::table('service_orders')
            ->join('users', 'service_orders.technician_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('COUNT(*) as completed_count'),
                DB::raw("$timeDiffSql as avg_repair_time"),
                DB::raw('SUM(service_cost) as total_revenue')
            )
            ->whereNotNull('technician_id')
            ->whereNotNull('repair_completed_at')
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();
    }

    private function getTopSellingProducts(): array
    {
        return DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();
    }

    private function getDeviceTypeDistribution(): array
    {
        return DB::table('service_orders')
            ->join('devices', 'service_orders.device_id', '=', 'devices.id')
            ->select('devices.type as device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('devices.type')
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();
    }

    private function getCustomerRetentionRate(): float
    {
        $totalCustomers = Customer::count();
        if ($totalCustomers === 0)
            return 0;

        $returningCustomers = Customer::has('serviceOrders', '>', 1)->count();
        return round(($returningCustomers / $totalCustomers) * 100, 2);
    }

    /** @return array<int, int> Jalali month (1-12) => count */
    public function getMonthlyServiceOrdersByJalaliYear(?int $jalaliYear = null): array
    {
        $jalaliYear = $jalaliYear ?? Jalalian::now()->getYear();
        $counts = array_fill(1, 12, 0);

        ServiceOrder::query()
            ->select(['id', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function ($orders) use (&$counts, $jalaliYear) {
                foreach ($orders as $order) {
                    $j = Jalalian::fromCarbon($order->created_at);
                    if ($j->getYear() === $jalaliYear) {
                        $counts[$j->getMonth()]++;
                    }
                }
            });

        return $counts;
    }

    /** @return array<string, array{label: string, count: int, color: string}> */
    public function getServiceOrderStatusBreakdown(): array
    {
        $raw = ServiceOrder::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $breakdown = [];
        foreach (ServiceOrderStatus::cases() as $status) {
            $count = (int) ($raw[$status->value] ?? 0);
            if ($count > 0) {
                $breakdown[$status->value] = [
                    'label' => $status->label(),
                    'count' => $count,
                    'color' => match ($status) {
                        ServiceOrderStatus::REGISTERED, ServiceOrderStatus::TECHNICIAN_ASSIGNED => 'bg-blue-500',
                        ServiceOrderStatus::REPAIRING, ServiceOrderStatus::PENDING_PARTS => 'bg-amber-500',
                        ServiceOrderStatus::READY => 'bg-emerald-500',
                        ServiceOrderStatus::DELIVERED => 'bg-teal-500',
                        ServiceOrderStatus::REJECTED => 'bg-rose-500',
                        ServiceOrderStatus::ACCOUNTING => 'bg-orange-500',
                        default => 'bg-slate-400',
                    },
                ];
            }
        }

        return $breakdown;
    }

    public function getOrderCounts(): array
    {
        $repair = ServiceOrder::count();
        $sales = Order::count();

        return [
            'repair' => $repair,
            'sales' => $sales,
            'total' => $repair + $sales,
        ];
    }
}
