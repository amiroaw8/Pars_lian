<?php

namespace App\DTOs;

class OrderDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly float $subtotal,
        public readonly float $tax_amount,
        public readonly float $shipping_amount,
        public readonly float $total_amount,
        public readonly string $payment_method,
        public readonly string $shipping_address,
        public readonly ?string $notes = null,
        public readonly ?string $coupon_code = null,
        public readonly float $discount_amount = 0,
        public readonly array $items = []
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            user_id: $validatedData['user_id'],
            subtotal: $validatedData['subtotal'],
            tax_amount: $validatedData['tax_amount'] ?? 0,
            shipping_amount: $validatedData['shipping_amount'] ?? 0,
            total_amount: $validatedData['total_amount'],
            payment_method: $validatedData['payment_method'],
            shipping_address: $validatedData['shipping_address'],
            notes: $validatedData['notes'] ?? null,
            coupon_code: $validatedData['coupon_code'] ?? null,
            discount_amount: $validatedData['discount_amount'] ?? 0,
            items: $validatedData['items'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'coupon_code' => $this->coupon_code,
            'discount_amount' => $this->discount_amount,
        ];
    }
}
