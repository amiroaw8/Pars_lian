<?php

namespace App\DTOs;

class ServiceOrderDTO
{
    public function __construct(
        public readonly int $customer_id,
        public readonly string $device_type,
        public readonly string $device_model,
        public readonly string $service_type,
        public readonly string $receiver_name,
        public readonly string $receiver_phone,
        public readonly string $fault,
        public readonly ?string $serial_number = null,
        public readonly ?string $asset_number = null,
        public readonly bool $has_guarantee = false,
        public readonly bool $is_warranty = false,
        public readonly ?string $warranty_id = null,
        public readonly ?string $user_department = null,
        public readonly ?string $accessories = null,
        public readonly ?string $notes = null,
        public readonly ?int $technician_id = null,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        $skipDevice = (bool) ($validatedData['skip_device_registration'] ?? false);

        return new self(
            customer_id: $validatedData['customer_id'],
            device_type: $skipDevice ? 'عدم ثبت' : ($validatedData['device_type'] ?? 'unknown'),
            device_model: $skipDevice ? '—' : ($validatedData['device_model'] ?? 'unknown'),
            service_type: $validatedData['service_type'] ?? 'in_company',
            receiver_name: $validatedData['receiver_name'] ?? '',
            receiver_phone: $validatedData['receiver_phone'] ?? '',
            fault: $validatedData['fault'] ?? '',
            serial_number: $validatedData['serial_number'] ?? null,
            asset_number: $validatedData['asset_number'] ?? null,
            has_guarantee: (bool) ($validatedData['has_guarantee'] ?? false),
            is_warranty: (bool) ($validatedData['is_warranty'] ?? false),
            warranty_id: $validatedData['warranty_id'] ?? null,
            user_department: $validatedData['user_department'] ?? null,
            accessories: $validatedData['accessories'] ?? null,
            notes: $validatedData['notes'] ?? null,
            technician_id: isset($validatedData['technician_id']) ? (int) $validatedData['technician_id'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'device_type' => $this->device_type,
            'device_model' => $this->device_model,
            'service_type' => $this->service_type,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'fault' => $this->fault,
            'serial_number' => $this->serial_number,
            'asset_number' => $this->asset_number,
            'has_guarantee' => $this->has_guarantee,
            'is_warranty' => $this->is_warranty,
            'warranty_id' => $this->warranty_id,
            'user_department' => $this->user_department,
            'accessories' => $this->accessories,
            'notes' => $this->notes,
            'technician_id' => $this->technician_id,
        ];
    }
}
