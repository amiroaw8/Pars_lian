<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderLog;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\SiteFileCatalog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
        private readonly SiteFileCatalog $siteFileCatalog,
    ) {
    }

    public function index()
    {
        $advancedStats = $this->analyticsService->getAdvancedAnalytics();
        $serviceStats = $this->analyticsService->getServiceOrderStats();
        $inventoryStats = $this->analyticsService->getInventoryStats();
        $customerStats = $this->analyticsService->getCustomerStats();
        $revenueStats = $this->analyticsService->getMonthlyRevenue();
        $currentMonthRevenue = $this->analyticsService->getCurrentJalaliMonthRevenue();
        $orderCounts = $this->analyticsService->getOrderCounts();
        $statusBreakdown = $this->analyticsService->getServiceOrderStatusBreakdown();
        $monthlyStats = collect($this->analyticsService->getMonthlyServiceOrdersByJalaliYear());

        try {
            $stats = [
                'total_users' => User::role(['admin', 'super_admin', 'technician', 'receptionist', 'warehouse', 'accountant'])->count(),
                'active_users' => User::role(['admin', 'super_admin', 'technician', 'receptionist', 'warehouse', 'accountant'])
                    ->where('is_active', true)
                    ->count(),
                'total_orders' => $orderCounts['total'],
                'repair_orders' => $orderCounts['repair'],
                'sales_orders' => $orderCounts['sales'],
                'pending_orders' => $serviceStats['registered'] + $serviceStats['repairing'],
                'completed_orders' => $serviceStats['delivered'],
                'total_attachments' => $this->siteFileCatalog->stats()['total'],
                'low_stock_items' => $inventoryStats['low_stock'],
                'customer_retention' => $advancedStats['customer_retention_rate'],
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard stats calculation error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            $stats = [
                'total_users' => 0,
                'active_users' => 0,
                'total_orders' => 0,
                'repair_orders' => 0,
                'sales_orders' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'total_attachments' => 0,
            ];
        }

        $recentActivities = OrderLog::with(['serviceOrder.customer', 'serviceOrder.device', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $userRoles = User::with('roles')
            ->whereHas('roles')
            ->get()
            ->flatMap(fn (User $user) => $user->getRoleNames())
            ->countBy();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'userRoles',
            'monthlyStats',
            'advancedStats',
            'inventoryStats',
            'customerStats',
            'revenueStats',
            'currentMonthRevenue',
            'statusBreakdown',
            'serviceStats',
        ));
    }
}
