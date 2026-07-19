<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Enums\ServiceOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getSummary()
    {
        $user = Auth::user();
        $notifications = [];

        // 1. Warehouse Alerts (Low Stock)
        if ($user->canManageInventory() || $user->isWarehouseManager()) {
            $lowStockCount = Product::where('stock_quantity', '<=', 5)->count();
            if ($lowStockCount > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'هشدار موجودی انبار',
                    'message' => "تعداد {$lowStockCount} محصول دارای موجودی کم هستند.",
                    'link' => route('automation.inventory.index'),
                    'icon' => 'ti-package-off'
                ];
            }
        }

        // 2. Receptionist Alerts (New Shop Orders)
        if ($user->isReceptionist() || $user->isAdmin()) {
            $newOrdersCount = Order::where('status', 'pending')->count();
            if ($newOrdersCount > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'title' => 'سفارشات جدید فروشگاه',
                    'message' => "تعداد {$newOrdersCount} سفارش جدید در انتظار بررسی است.",
                    'link' => route('automation.orders.index'),
                    'icon' => 'ti-shopping-cart'
                ];
            }
        }

        // 3. Reception alerts: orders without assigned technician
        if ($user->isReceptionist() || $user->isAdmin() || $user->isSuperAdmin()) {
            $unassignedCount = ServiceOrder::whereNull('technician_id')
                ->whereIn('status', [ServiceOrderStatus::REGISTERED, ServiceOrderStatus::TECHNICIAN_ASSIGNED])
                ->count();
            if ($unassignedCount > 0) {
                $notifications[] = [
                    'type' => 'primary',
                    'title' => 'سفارش بدون تکنسین',
                    'message' => "تعداد {$unassignedCount} سفارش هنوز تکنسین مسئول ندارد.",
                    'link' => route('automation.service-orders.index'),
                    'icon' => 'ti-user-cog',
                ];
            }
        }

        // 4. Accounting Alerts (Pending Payments)
        if ($user->canManageAccounting()) {
            $pendingAccounting = ServiceOrder::where('status', ServiceOrderStatus::ACCOUNTING)->count();
            if ($pendingAccounting > 0) {
                $notifications[] = [
                    'type' => 'success',
                    'title' => 'در انتظار تسویه حساب',
                    'message' => "تعداد {$pendingAccounting} سفارش تعمیر آماده تسویه مالی هستند.",
                    'link' => route('automation.dashboard.accounting'),
                    'icon' => 'ti-receipt-2'
                ];
            }
        }

        return response()->json($notifications);
    }
}
