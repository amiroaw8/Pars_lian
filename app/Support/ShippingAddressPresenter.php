<?php

namespace App\Support;

use App\Models\Order;

class ShippingAddressPresenter
{
    /** @var list<string> */
    private const PLACEHOLDER_VALUES = ['نامشخص', '-', '—', 'n/a', 'na'];

    public function __construct(private readonly Order $order) {}

    public static function for(Order $order): self
    {
        return new self($order);
    }

    public function isPickup(): bool
    {
        return $this->order->shipping_method === 'pickup';
    }

    public function locationLine(): ?string
    {
        $parts = [];

        foreach (['shipping_state', 'shipping_city'] as $field) {
            $value = $this->clean((string) ($this->order->{$field} ?? ''));

            if ($value !== null) {
                $parts[] = $value;
            }
        }

        return $parts === [] ? null : implode('، ', $parts);
    }

    public function streetLine(): ?string
    {
        $address = $this->clean((string) ($this->order->shipping_address ?? ''));

        if ($address === null) {
            return null;
        }

        if ($this->isPickup() && $address === 'تحویل حضوری') {
            return null;
        }

        return $address;
    }

    public function postalCode(): ?string
    {
        return $this->clean((string) ($this->order->shipping_postal_code ?? ''));
    }

  /**
     * @return list<string>
     */
    public function lines(): array
    {
        $lines = [];

        if ($location = $this->locationLine()) {
            $lines[] = $location;
        }

        if ($street = $this->streetLine()) {
            $lines[] = $street;
        }

        if ($lines === [] && $this->isPickup()) {
            $lines[] = 'تحویل حضوری از فروشگاه';
        }

        return $lines;
    }

    public function isEmpty(): bool
    {
        return $this->lines() === [] && $this->postalCode() === null;
    }

    public function singleLine(): string
    {
        $parts = $this->lines();

        if ($postal = $this->postalCode()) {
            $parts[] = 'کد پستی: '.$postal;
        }

        if ($parts === []) {
            return 'آدرسی ثبت نشده است';
        }

        return implode(' — ', $parts);
    }

    private function clean(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        foreach (self::PLACEHOLDER_VALUES as $placeholder) {
            if (mb_strtolower($value) === mb_strtolower($placeholder)) {
                return null;
            }
        }

        return $value;
    }
}
