<?php

namespace App\Support;

use App\Models\Customer;

class OrderShippingDefaults
{
    /** @return array<string, string|null> */
    public static function fromCustomer(Customer $customer): array
    {
        return [
            'shipping_first_name' => self::firstName($customer),
            'shipping_last_name' => self::lastName($customer),
            'shipping_phone' => PhoneNumber::digits($customer->phone) ?: $customer->phone,
            'shipping_address' => self::address($customer),
            'shipping_city' => self::requiredCity($customer->user?->city),
            'shipping_state' => self::optional($customer->user?->province),
        ];
    }

    public static function requiredCity(?string $city): string
    {
        $city = trim((string) $city);

        return $city !== '' ? $city : '—';
    }

    public static function optional(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private static function firstName(Customer $customer): string
    {
        $name = trim((string) ($customer->name ?? ''));

        return $name !== '' ? $name : 'مشتری';
    }

    private static function lastName(Customer $customer): string
    {
        return '';
    }

    private static function address(Customer $customer): string
    {
        $address = trim((string) ($customer->address ?? ''));

        return $address !== '' ? $address : 'تحویل حضوری';
    }
}
