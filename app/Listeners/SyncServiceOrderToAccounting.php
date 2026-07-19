<?php

namespace App\Listeners;

use App\Events\ServiceOrderStatusChanged;
use App\Enums\ServiceOrderStatus;
use App\Services\AccountingManager;
use App\Models\AccountingService;

class SyncServiceOrderToAccounting
{
    public function __construct(
        private AccountingManager $accountingManager
    ) {}

    /**
     * Handle the event.
     */
    public function handle(ServiceOrderStatusChanged $event): void
    {
        $order = $event->order;

        // Only record service if status is DELIVERED
        if ($order->status !== ServiceOrderStatus::DELIVERED) {
            return;
        }

        // Check if service already recorded to avoid duplicates
        $exists = AccountingService::where('service_order_id', $order->id)->exists();
        if ($exists) {
            return;
        }

        $this->accountingManager->recordService(
            amount: $order->total_cost,
            description: "درآمد خدمات تعمیر سفارش {$order->order_number}",
            serviceOrderId: $order->id,
            technicianId: $order->technician_id,
            transactionDate: now()->toDateTimeString()
        );
    }
}
