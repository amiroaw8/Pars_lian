<?php

namespace App\Observers;

use App\Enums\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Cache;

class ServiceOrderObserver
{
    /**
     * Handle the ServiceOrder "created" event.
     */
    public function created(ServiceOrder $serviceOrder): void
    {
        $this->invalidateCache($serviceOrder);
    }

    /**
     * Handle the ServiceOrder "updated" event.
     */
    public function updated(ServiceOrder $serviceOrder): void
    {
        $this->invalidateCache($serviceOrder);

        $statusChanged = $serviceOrder->wasChanged('status');
        $newStatus = $serviceOrder->status;
        $originalStatus = $serviceOrder->getOriginal('status');

        if ($statusChanged && $newStatus === ServiceOrderStatus::REJECTED) {
            $this->restoreInventoryForRejectedOrder($serviceOrder);
        }

        if ($statusChanged && $newStatus === ServiceOrderStatus::ARCHIVED && $originalStatus !== ServiceOrderStatus::DELIVERED->value) {
            $this->restoreInventoryForRejectedOrder($serviceOrder);
        }
    }

    /**
     * Restore stock for repair items when a service order is rejected.
     */
    protected function restoreInventoryForRejectedOrder(ServiceOrder $serviceOrder): void
    {
        foreach ($serviceOrder->repairItems as $repairItem) {
            if ($repairItem->inventory_id && $repairItem->inventory && $repairItem->quantity > 0) {
                try {
                    $repairItem->inventory->updateStock(
                        $repairItem->quantity,
                        'return',
                        "برگشت از تعمیر رد شده - سفارش #{$serviceOrder->id}"
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error(
                        "Failed to restore inventory for rejected service order {$serviceOrder->id}: {$e->getMessage()}"
                    );
                }
            }
        }
    }

    /**
     * Handle the ServiceOrder "deleted" event.
     */
    public function deleted(ServiceOrder $serviceOrder): void
    {
        $this->invalidateCache($serviceOrder);
    }

    /**
     * Handle the ServiceOrder "restored" event.
     */
    public function restored(ServiceOrder $serviceOrder): void
    {
        $this->invalidateCache($serviceOrder);
    }

    /**
     * Handle the ServiceOrder "force deleted" event.
     */
    public function forceDeleted(ServiceOrder $serviceOrder): void
    {
        $this->invalidateCache($serviceOrder);
    }

    /**
     * Invalidate dashboard caches.
     */
    protected function invalidateCache(ServiceOrder $serviceOrder): void
    {
        Cache::forget('dashboard_global_stats');
        Cache::forget('service_order_stats');
        
        if ($serviceOrder->technician_id) {
            Cache::forget('dashboard_user_stats_' . $serviceOrder->technician_id);
        }
    }
}
