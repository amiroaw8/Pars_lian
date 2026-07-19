<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-unpaid-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Identifies unpaid orders older than 2 hours, cancels them, and restores product stock.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiryTime = now()->subHours(2);

        $expiredOrders = Order::query()
            ->where('payment_status', PaymentStatus::PENDING)
            ->where('status', OrderStatus::PENDING)
            ->where('created_at', '<=', $expiryTime)
            ->with('items.product')
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No unpaid expired orders found.');
            return self::SUCCESS;
        }

        $this->info("Found {$expiredOrders->count()} expired unpaid orders. Processing...");

        foreach ($expiredOrders as $order) {
            /** @var \App\Models\Order $order */
            $this->info("Processing Order #{$order->order_number} (ID: {$order->id})...");
            
            DB::beginTransaction();
            try {
                /** @var \App\Models\OrderItem $item */
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->restoreStock($item->quantity);
                        $this->line(" - Restored {$item->quantity} x {$item->product->name} (SKU: {$item->product->sku})");
                    }
                }

                $order->update(['status' => OrderStatus::CANCELLED]);
                
                DB::commit();
                $this->info("Order #{$order->order_number} successfully cancelled and stock restored.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to process Order #{$order->order_number}: " . $e->getMessage());
                \Illuminate\Support\Facades\Log::error("CleanupUnpaidOrders failed for order {$order->id}: " . $e->getMessage());
            }
        }

        $this->info('Cleanup task completed.');
        return self::SUCCESS;
    }
}
