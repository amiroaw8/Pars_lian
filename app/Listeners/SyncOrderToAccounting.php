<?php

namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use App\Enums\PaymentStatus;
use App\Services\AccountingManager;
use App\Models\AccountingSale;

class SyncOrderToAccounting
{
    public function __construct(
        private AccountingManager $accountingManager
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PaymentStatusChanged $event): void
    {
        $order = $event->order;

        // Only record sale if payment status is PAID
        if ($order->payment_status !== PaymentStatus::PAID) {
            return;
        }

        // Check if sale already recorded to avoid duplicates
        $exists = AccountingSale::where('order_id', $order->id)->exists();
        if ($exists) {
            return;
        }

        $this->accountingManager->recordSale(
            amount: $order->total,
            description: "فروش خودکار بابت سفارش شماره {$order->order_number}",
            customerId: $order->user?->customer?->id,
            orderId: $order->id,
            transactionDate: now()->toDateTimeString(),
            paymentMethod: $order->payment_method ?? 'cash'
        );
    }
}
