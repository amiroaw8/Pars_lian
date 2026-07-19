<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\User;
use App\Jobs\SendSmsJob;
use App\Support\SmsNotifications;
use Illuminate\Support\Facades\Log;

class InventoryObserver
{
    /**
     * Handle the Inventory "updated" event.
     */
    public function updated(Inventory $inventory): void
    {
        // Check if quantity has changed
        if ($inventory->wasChanged('quantity')) {
            $newQuantity = $inventory->quantity;
            $minQuantity = $inventory->min_quantity;
            $originalQuantity = $inventory->getOriginal('quantity');

            // Check if stock dropped to or below minimum
            // And ensure it wasn't already low (to avoid spamming on every small change when already low)
            if ($newQuantity <= $minQuantity && $originalQuantity > $minQuantity) {
                $this->sendLowStockAlert($inventory);
            }
        }
    }

    /**
     * Send low stock alert SMS to warehouse managers.
     */
    protected function sendLowStockAlert(Inventory $inventory): void
    {
        if (! SmsNotifications::isInventoryAlertEnabled()) {
            return;
        }

        try {
            // Find all users with 'warehouse' role
            // We use the Spatie permission scope if available, or filter manually
            $warehouseManagers = User::role('warehouse')->get();
            
            if ($warehouseManagers->isEmpty()) {
                // Fallback to admin if no warehouse manager
                $warehouseManagers = User::role('admin')->get();
            }

            foreach ($warehouseManagers as $manager) {
                if ($manager->phone) {
                    $message = SmsNotifications::prepareInventoryItemAlertMessage($inventory);

                    SendSmsJob::dispatch($manager->phone, $message);
                    
                    Log::info("Low stock alert queued for {$inventory->name} to {$manager->phone}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to send low stock alert: " . $e->getMessage());
        }
    }
}
