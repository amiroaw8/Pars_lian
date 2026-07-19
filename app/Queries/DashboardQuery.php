<?php

namespace App\Queries;

use App\Models\ServiceOrder;
use App\Models\Order;
use App\Models\Customer;
use App\Models\AccountingSale;
use App\Models\Inventory;
use App\Models\User;
use App\Enums\ServiceOrderStatus;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardQuery
{
    /**
     * Get global statistics for the dashboard.
     */
    public function getGlobalStats(): array
    {
        return Cache::remember('dashboard_global_stats', 3600, function () {
            return [
                'repair_stats' => [
                    'total' => ServiceOrder::count(),
                    'pending' => ServiceOrder::whereIn('status', [
                        ServiceOrderStatus::REGISTERED->value,
                        ServiceOrderStatus::REPAIRING->value
                    ])->count(),
                    'ready' => ServiceOrder::where('status', ServiceOrderStatus::READY->value)->count(),
                    'delivered' => ServiceOrder::where('status', ServiceOrderStatus::DELIVERED->value)->count(),
                ],
                'sales_stats' => [
                    'total' => Order::count(),
                    'pending' => Order::where('status', OrderStatus::PENDING->value)->count(),
                    'processing' => Order::where('status', OrderStatus::PROCESSING->value)->count(),
                    'shipped' => Order::where('status', OrderStatus::SHIPPED->value)->count(),
                    'delivered' => Order::where('status', OrderStatus::DELIVERED->value)->count(),
                ],
                'inventory_stats' => [
                    'low_stock' => Inventory::where('quantity', '<=', config('settings.low_stock_threshold', 5))->count(),
                ]
            ];
        });
    }

    /**
     * Get statistics specific to the current user's role.
     */
    public function getUserStats(User $user): array
    {
        return Cache::remember('dashboard_user_stats_' . $user->id, 600, function () use ($user) {
            $data = [];

            if ($user->isTechnician()) {
                $data['my_active_repairs'] = ServiceOrder::where('status', ServiceOrderStatus::REPAIRING->value)
                    ->where('technician_id', $user->id)->count();
                
                $data['pending_assignment'] = ServiceOrder::where('status', ServiceOrderStatus::REGISTERED->value)
                    ->whereNull('technician_id')->count();
            }

            if ($user->isReceptionist()) {
                $data['today_registrations'] = ServiceOrder::whereDate('created_at', today())->count();
                $data['today_sales_orders'] = Order::whereDate('created_at', today())->count();
                $data['pending_delivery'] = ServiceOrder::where('status', ServiceOrderStatus::READY->value)->count();
                $data['total_customers'] = Customer::count();
            }

            if ($user->isAccountant() || $user->isAdmin() || $user->isSuperAdmin()) {
                $data['today_sales'] = AccountingSale::whereDate('created_at', today())->sum('amount');
                $data['monthly_income'] = AccountingSale::whereMonth('created_at', now()->month)->sum('amount');
            }

            return $data;
        });
    }
}
