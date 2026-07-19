<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Models\Device;
use App\Models\Attachment;
use App\Models\OrderLog;
use App\Enums\ServiceOrderStatus;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Interfaces\ServiceOrderRepositoryInterface;
use App\DTOs\ServiceOrderDTO;

class OrderService
{
    public function __construct(
        private ServiceOrderRepositoryInterface $orderRepository
    ) {}

    /**
     * Create a new service order with its device.
     */
    public function createOrder(array|ServiceOrderDTO $data, ?array $attachments = null): ServiceOrder
    {
        if (is_array($data)) {
            $data = ServiceOrderDTO::fromRequest($data);
        }

        return DB::transaction(function () use ($data, $attachments) {
            // 1. Create Device
            $device = Device::create([
                'customer_id' => $data->customer_id,
                'type' => $data->device_type,
                'model' => $data->device_model,
                'serial_number' => $data->serial_number,
                'asset_number' => $data->asset_number,
                'has_guarantee' => $data->has_guarantee,
            ]);

            // 2. Create Service Order
            $customer = \App\Models\Customer::find($data->customer_id);
            $receiverPhone = trim($data->receiver_phone ?? '');
            if ($receiverPhone === '' && $customer) {
                $receiverPhone = trim($customer->phone ?? '');
            }

            $order = $this->orderRepository->create([
                'customer_id' => $data->customer_id,
                'device_id' => $device->id,
                'service_type' => $data->service_type,
                'receiver_name' => $data->receiver_name ?: ($customer->name ?? ''),
                'receiver_phone' => $receiverPhone,
                'user_department' => $data->user_department,
                'accessories' => $data->accessories,
                'fault' => $data->fault,
                'is_warranty' => $data->is_warranty,
                'warranty_id' => $data->warranty_id,
                'notes' => $data->notes,
                'status' => ServiceOrderStatus::REGISTERED,
            ]);

            // 3. Process Attachments
            if ($attachments) {
                $this->processAttachments($attachments, $order);
            }

            // 4. Log Creation
            $this->logStatusChange($order, null, ServiceOrderStatus::REGISTERED, 'سفارش ثبت شد.');

            // 5. Assign technician at intake (reception selects on create)
            if ($data->technician_id) {
                $this->assignTechnician($order, $data->technician_id);
            }

            // 6. Dispatch Event
            event(new \App\Events\ServiceOrderCreated($order));

            return $order->fresh();
        });
    }

    /**
     * Update an existing service order.
     */
    public function updateOrder(ServiceOrder $order, array|ServiceOrderDTO $data, ?array $attachments = null): ServiceOrder
    {
        if (is_array($data)) {
            $data = ServiceOrderDTO::fromRequest($data);
        }

        return DB::transaction(function () use ($order, $data, $attachments) {
            // Update Device
            $order->device->update([
                'customer_id' => $data->customer_id,
                'type' => $data->device_type,
                'model' => $data->device_model,
                'serial_number' => $data->serial_number,
                'asset_number' => $data->asset_number,
                'has_guarantee' => $data->has_guarantee,
            ]);

            $orderData = $data->toArray();
            unset($orderData['technician_id']);
            $this->orderRepository->update($order->id, $orderData);
            $order->refresh();

            if ($data->technician_id && (int) $order->technician_id !== $data->technician_id) {
                $this->assignTechnician($order, $data->technician_id);
            }

            // Process Attachments
            if ($attachments) {
                $this->processAttachments($attachments, $order);
            }

            return $order;
        });
    }

    /**
     * Update the status of a service order.
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function updateStatus(ServiceOrder $order, string|ServiceOrderStatus $newStatus, ?string $note = null, bool $force = false): ServiceOrder
    {
        if (is_string($newStatus)) {
            $newStatus = ServiceOrderStatus::from($newStatus);
        }

        if ($order->status === $newStatus) {
            return $order;
        }

        // Validate status transition (unless forced)
        if (! $force && ! $this->isValidStatusTransition($order->status, $newStatus)) {
            $error = "انتقال وضعیت نامعتبر از '{$order->status->label()}' به '{$newStatus->label()}' برای سفارش #{$order->id}";
            Log::error('Invalid status transition attempt', [
                'service_order_id' => $order->id,
                'current_status' => $order->status->value,
                'attempted_status' => $newStatus->value,
                'user_id' => Auth::id(),
            ]);
            throw new \InvalidArgumentException($error);
        }

        return DB::transaction(function () use ($order, $newStatus, $note, $force) {
            $oldStatus = $order->status;
            
            $updateData = ['status' => $newStatus];

            // Set timestamps based on status
            if ($newStatus === ServiceOrderStatus::REPAIRING && ! $order->repair_started_at) {
                $updateData['repair_started_at'] = now();
            } elseif (in_array($newStatus, [ServiceOrderStatus::READY, ServiceOrderStatus::DELIVERED]) && ! $order->repair_completed_at) {
                $updateData['repair_completed_at'] = now();
            }

            $this->orderRepository->update($order->id, $updateData);
            $order->refresh();

            // 145. Log change
            $description = $note ?? "وضعیت از '{$oldStatus?->label()}' به '{$newStatus->label()}' تغییر یافت.";
            if ($force) {
                $description .= " (تغییر وضعیت دستی/اجباری)";
            }
            $this->logStatusChange($order, $oldStatus, $newStatus, $description);

            // 155. Dispatch Event
            event(new \App\Events\ServiceOrderStatusChanged($order));

            return $order;
        });
    }

    /**
     * Log status change in OrderLog
     */
    protected function logStatusChange(ServiceOrder $order, ?ServiceOrderStatus $oldStatus, ServiceOrderStatus $newStatus, ?string $note = null): void
    {
        OrderLog::create([
            'service_order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'status_change',
            'old_value' => $oldStatus?->value,
            'new_value' => $newStatus->value,
            'description' => $note ?? "وضعیت از '{$oldStatus?->label()}' به '{$newStatus->label()}' تغییر یافت.",
            'changes' => [
                'status' => [
                    'old' => $oldStatus?->value,
                    'new' => $newStatus->value,
                ]
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Process and store attachments
     */
    public function processAttachments(array $files, ServiceOrder $order): void
    {
        foreach ($files as $file) {
            $path = FileStorage::storePrivate($file, 'attachments');

            Attachment::create([
                'attachable_id' => $order->id,
                'attachable_type' => ServiceOrder::class,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Validate if a status transition is allowed
     */
    protected function isValidStatusTransition(ServiceOrderStatus $currentStatus, ServiceOrderStatus $newStatus): bool
    {
        $validTransitions = [
            ServiceOrderStatus::REGISTERED->value => [
                ServiceOrderStatus::TECHNICIAN_ASSIGNED->value,
                ServiceOrderStatus::REJECTED->value,
                ServiceOrderStatus::READY->value,
                ServiceOrderStatus::DELIVERED->value,
                ServiceOrderStatus::ARCHIVED->value
            ],
            ServiceOrderStatus::TECHNICIAN_ASSIGNED->value => [
                ServiceOrderStatus::REPAIRING->value,
                ServiceOrderStatus::REJECTED->value,
                ServiceOrderStatus::REGISTERED->value // Unassign
            ],
            ServiceOrderStatus::REPAIRING->value => [
                ServiceOrderStatus::READY->value,
                ServiceOrderStatus::REJECTED->value,
                ServiceOrderStatus::ACCOUNTING->value,
                ServiceOrderStatus::TECHNICIAN_ASSIGNED->value, // Reassign
                ServiceOrderStatus::REPAIRING->value // Update info
            ],
            ServiceOrderStatus::REJECTED->value => [
                ServiceOrderStatus::DELIVERED->value,
                ServiceOrderStatus::ARCHIVED->value,
                ServiceOrderStatus::REPAIRING->value // Re-attempt
            ],
            ServiceOrderStatus::ACCOUNTING->value => [
                ServiceOrderStatus::READY->value,
                ServiceOrderStatus::DELIVERED->value,
                ServiceOrderStatus::REPAIRING->value // Billing issue/Re-check
            ],
            ServiceOrderStatus::READY->value => [
                ServiceOrderStatus::DELIVERED->value,
                ServiceOrderStatus::ACCOUNTING->value, // If payment pending
                ServiceOrderStatus::REPAIRING->value // Return to repair
            ],
            ServiceOrderStatus::DELIVERED->value => [
                ServiceOrderStatus::ARCHIVED->value,
                ServiceOrderStatus::READY->value, // Return
                ServiceOrderStatus::REPAIRING->value // Warranty return
            ],
            ServiceOrderStatus::ARCHIVED->value => [
                ServiceOrderStatus::REGISTERED->value, // Re-open
                ServiceOrderStatus::DELIVERED->value
            ],
        ];

        return in_array($newStatus->value, $validTransitions[$currentStatus->value] ?? []);
    }

    /**
     * Move order to accounting status
     */
    public function toAccounting(ServiceOrder $order): ServiceOrder
    {
        return $this->updateStatus($order, ServiceOrderStatus::ACCOUNTING);
    }

    /**
     * Mark order as ready for delivery
     */
    public function toReadyForDelivery(ServiceOrder $order): ServiceOrder
    {
        return $this->updateStatus($order, ServiceOrderStatus::READY);
    }

    /**
     * Mark order as delivered
     */
    public function toDelivered(ServiceOrder $order): ServiceOrder
    {
        return $this->updateStatus($order, ServiceOrderStatus::DELIVERED);
    }

    /**
     * Archive the order
     */
    public function toArchived(ServiceOrder $order): ServiceOrder
    {
        return $this->updateStatus($order, ServiceOrderStatus::ARCHIVED);
    }

    /**
     * Reject the order
     */
    public function reject(ServiceOrder $order, string $reason): ServiceOrder
    {
        return $this->updateStatus($order, ServiceOrderStatus::REJECTED, $reason);
    }

    /**
     * Assign a technician to the order
     */
    public function assignTechnician(ServiceOrder $order, int $technicianId): ServiceOrder
    {
        return DB::transaction(function () use ($order, $technicianId) {
            $oldStatus = $order->status;
            $newStatus = ServiceOrderStatus::TECHNICIAN_ASSIGNED;
            
            // Allow re-assignment without status check if status is already assigned or repairing
            if (!in_array($oldStatus, [ServiceOrderStatus::REGISTERED, ServiceOrderStatus::TECHNICIAN_ASSIGNED, ServiceOrderStatus::REPAIRING])) {
                 throw new \InvalidArgumentException("نمی‌توان در وضعیت فعلی تکنسین تعیین کرد.");
            }

            $order->update([
                'technician_id' => $technicianId,
                'status' => $newStatus,
            ]);
            $order->refresh();

            $this->logStatusChange($order, $oldStatus, $newStatus, 'تکنسین تعیین شد.');

            event(new \App\Events\ServiceOrderStatusChanged($order));

            return $order;
        });
    }
}
