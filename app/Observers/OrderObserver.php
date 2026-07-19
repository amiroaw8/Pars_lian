<?php

namespace App\Observers;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $status = $order->status instanceof \UnitEnum ? $order->status->value : $order->status;
        $originalStatus = $order->getOriginal('status');
        $originalStatusValue = $originalStatus instanceof \UnitEnum ? $originalStatus->value : $originalStatus;
        
        Log::info("Order updated: {$order->id}, status: {$status}, original: {$originalStatusValue}");
        
        // If status changed to CANCELLED, restore stock
        if ($order->wasChanged('status') && $order->status === OrderStatus::CANCELLED) {
            Log::info("Restoring stock for order {$order->id}");
            $this->restoreStock($order);
        }
        
        // If status changed to DELIVERED, confirm stock deduction (stock already reduced at order creation)
        if ($order->wasChanged('status') && $order->status === OrderStatus::DELIVERED) {
            Log::info("Order {$order->id} delivered - stock already deducted at creation");
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Should we restore stock on delete? Usually cancellation is preferred.
        // But if hard deleted, maybe yes. For soft delete, probably not unless status changes.
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }

    /**
     * Restore stock for order items
     */
    protected function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product) {
                try {
                    $item->product->restoreStock($item->quantity);
                    Log::info("Stock restored for product {$item->product_id} from cancelled order {$order->order_number}");
                } catch (\Exception $e) {
                    Log::error("Failed to restore stock for product {$item->product_id} from cancelled order {$order->order_number}: " . $e->getMessage());
                    throw $e;
                }
            }
        }
    }
}
