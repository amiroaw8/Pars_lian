<?php

namespace App\Services;

use App\Models\RepairItem;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

class RepairService
{
    public function __construct(
        private OrderService $orderService,
        private AccountingManager $accountingManager
    ) {
    }

    /**
     * Add a repair item to a service order.
     */
    public function addItem(ServiceOrder $order, array $data): RepairItem
    {
        return DB::transaction(function () use ($order, $data) {
            $rawCost = $data['cost'] ?? null;
            $cost = ($rawCost !== null && $rawCost !== '') ? (float) $rawCost : 0.0;

            // If it's a warranty repair, part/service cost should be 0 for the customer
            if ($order->is_warranty) {
                $cost = 0.0;
            }

            unset($data['cost']);
            if (array_key_exists('description', $data) && $data['description'] === null) {
                unset($data['description']);
            }

            $item = $order->repairItems()->create(array_merge($data, [
                'cost' => $cost,
                'sort_order' => $order->repairItems()->count(),
            ]));

            // If it's an inventory item, reduce stock
            if ($item->inventory_id) {
                $item->inventory->updateStock(
                    -$item->quantity,
                    'use',
                    "استفاده در تعمیر — سفارش تعمیر #{$order->id}"
                );
            }

            $order->recalculateServiceCost();

            return $item;
        });
    }

    /**
     * Update a repair item.
     */
    public function updateItem(RepairItem $item, array $data): RepairItem
    {
        return DB::transaction(function () use ($item, $data) {
            $oldQuantity = $item->quantity;
            $newQuantity = $data['quantity'] ?? $oldQuantity;

            // Update stock if inventory item and quantity changed
            if ($item->inventory_id && $oldQuantity != $newQuantity) {
                $diff = $newQuantity - $oldQuantity;
                if ($diff > 0) {
                    // Increased quantity, consume more stock
                    $item->inventory->updateStock(
                        -$diff,
                        'use',
                        "اصلاحیه (افزایش) - سفارش #{$item->service_order_id}"
                    );
                } else {
                    // Decreased quantity, return to stock
                    $item->inventory->updateStock(
                        abs($diff),
                        'return',
                        "اصلاحیه (کاهش) - سفارش #{$item->service_order_id}"
                    );
                }
            }

            $item->update($data);
            $item->serviceOrder->recalculateServiceCost();

            return $item;
        });
    }

    /**
     * Remove a repair item and restore stock if necessary.
     */
    public function removeItem(RepairItem $item): void
    {
        DB::transaction(function () use ($item) {
            $order = $item->serviceOrder;

            // If it's an inventory item, restore stock
            if ($item->inventory_id) {
                $item->inventory->updateStock(
                    $item->quantity,
                    'return',
                    "برگشت از تعمیر - سفارش #{$order->id}"
                );
            }

            $item->delete();
            $order->recalculateServiceCost();
        });
    }

    /**
     * Update repair costs for multiple items.
     */
    public function updateCosts(ServiceOrder $order, array $costs): void
    {
        DB::transaction(function () use ($order, $costs) {
            foreach ($costs as $itemId => $cost) {
                $order->repairItems()->where('id', $itemId)->update(['cost' => $cost]);
            }
            $order->recalculateServiceCost();
        });
    }

    /**
     * Assign a technician to a service order.
     */
    public function assignTechnician(ServiceOrder $order, int $technicianId): void
    {
        $this->orderService->assignTechnician($order, $technicianId);
    }

    /**
     * Start the repair process.
     */
    public function startRepair(ServiceOrder $order): void
    {
        $this->orderService->updateStatus($order, \App\Enums\ServiceOrderStatus::REPAIRING, 'تعمیر دستگاه شروع شد.');
    }

    /**
     * Complete the repair process.
     */
    public function completeRepair(ServiceOrder $order): void
    {
        // Always move to ACCOUNTING for verification, even if cost is zero
        $status = \App\Enums\ServiceOrderStatus::ACCOUNTING;
        $message = 'تعمیر انجام شد. سفارش به حسابداری ارسال گردید.';

        $this->orderService->updateStatus($order, $status, $message);
    }

    /**
     * Verify payment and move to Ready for Delivery.
     */
    public function verifyPayment(ServiceOrder $order, array $costs = [], float $taxPercent = 0): void
    {
        DB::transaction(function () use ($order, $costs, $taxPercent) {
            // Update item costs if provided
            if (!empty($costs)) {
                foreach ($costs as $itemId => $cost) {
                    $order->repairItems()->where('id', $itemId)->update(['cost' => $cost]);
                }
                $order->recalculateServiceCost();
            }

            // Calculate base amount
            $baseAmount = $order->repairItems()->sum(DB::raw('cost * quantity'));

            $debt = (float) ($order->debt_amount ?? 0);
            if ($debt > 0) {
                $order->update([
                    'debt_amount' => max(0, $debt - $baseAmount),
                ]);
            }

            $this->accountingManager->recordService(
                $baseAmount,
                "[بدهی] هزینه تعمیر - سفارش #{$order->id}",
                $order->id,
                $order->technician_id,
                null,
                0
            );

            $this->accountingManager->recordService(
                $baseAmount,
                "[پرداخت] پرداخت هزینه تعمیر - سفارش #{$order->id}",
                $order->id,
                $order->technician_id,
                null,
                0
            );

            // Change status to Ready
            $this->orderService->toReadyForDelivery($order);
        });
    }

    /**
     * Mark as delivered.
     */
    public function deliver(ServiceOrder $order): void
    {
        $this->orderService->toDelivered($order);
    }

    /**
     * Register customer debt using finalized repair costs and mark ready for delivery.
     */
    public function recordDebt(ServiceOrder $order, ?string $reason = null): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order->update([
                'debt_amount' => (float) ($order->service_cost ?? 0),
                'debt_reason' => $reason ?: 'بدهی مشتری پس از اتمام تعمیر',
            ]);

            \App\Models\AccountingService::create([
                'service_order_id' => $order->id,
                'technician_id' => $order->technician_id,
                'amount' => (float) ($order->service_cost ?? 0),
                'description' => '[بدهی] '.($reason ?: 'بدهی مشتری پس از اتمام تعمیر'),
                'transaction_date' => now(),
                'payment_status' => 'unpaid',
            ]);

            $this->orderService->toReadyForDelivery($order);
        });
    }

    /**
     * Settle outstanding repair debt and record payment in accounting.
     */
    public function settleDebt(ServiceOrder $order, ?float $amount = null): void
    {
        DB::transaction(function () use ($order, $amount) {
            $order->refresh();
            $debt = (float) ($order->debt_amount ?? 0);

            if ($debt <= 0) {
                throw new \InvalidArgumentException('بدهی فعالی برای تسویه وجود ندارد.');
            }

            $paidAmount = $amount ?? $debt;
            $paidAmount = min(max(0, $paidAmount), $debt);
            $remaining = max(0, $debt - $paidAmount);

            \App\Models\AccountingService::query()
                ->where('service_order_id', $order->id)
                ->where('payment_status', 'unpaid')
                ->update(['payment_status' => 'paid']);

            $this->accountingManager->recordService(
                $paidAmount,
                "[پرداخت] تسویه بدهی تعمیر - سفارش #{$order->id}",
                $order->id,
                $order->technician_id,
                null,
                0
            );

            $order->update(['debt_amount' => $remaining]);
        });
    }

    /**
     * Reorder repair items by ID list.
     *
     * @param  list<int>  $orderedIds
     */
    public function reorderItems(ServiceOrder $order, array $orderedIds): void
    {
        DB::transaction(function () use ($order, $orderedIds) {
            foreach ($orderedIds as $index => $itemId) {
                $order->repairItems()->where('id', $itemId)->update(['sort_order' => $index]);
            }
        });
    }
}
