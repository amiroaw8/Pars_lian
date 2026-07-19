<?php

namespace App\Support;

use App\Enums\ServiceOrderStatus;
use BackedEnum;

class ActivityLogFormatter
{
    /** @var array<string, string> */
    private const MODEL_LABELS = [
        'ServiceOrder' => 'سفارش تعمیر',
        'Customer' => 'مشتری',
        'Product' => 'محصول',
        'Order' => 'سفارش فروش',
        'User' => 'کاربر',
        'Device' => 'دستگاه',
        'Attachment' => 'فایل پیوست',
        'Inventory' => 'کالای انبار',
        'RepairItem' => 'آیتم تعمیر',
        'ProductCategory' => 'دسته محصول',
        'Brand' => 'برند',
        'CustomerInteraction' => 'تعامل مشتری',
        'OrderItem' => 'قلم سفارش',
        'InventoryTransaction' => 'تراکنش انبار',
    ];

    /** @var array<string, string> */
    private const EVENT_LABELS = [
        'created' => 'ایجاد رکورد جدید',
        'updated' => 'ویرایش اطلاعات',
        'deleted' => 'حذف رکورد',
    ];

    /** @var array<string, string> */
    private const FIELD_LABELS = [
        'status' => 'وضعیت',
        'debt_amount' => 'مبلغ بدهی',
        'debt_reason' => 'دلیل بدهی',
        'service_cost' => 'هزینه خدمات',
        'technician_id' => 'تکنسین',
        'customer_id' => 'مشتری',
        'device_id' => 'دستگاه',
        'receiver_name' => 'نام تحویل‌دهنده',
        'receiver_phone' => 'تلفن',
        'fault' => 'ایراد فنی',
        'notes' => 'توضیحات',
        'name' => 'نام',
        'email' => 'ایمیل',
        'phone' => 'تلفن',
        'is_active' => 'وضعیت فعال بودن',
        'price' => 'قیمت',
        'stock_quantity' => 'موجودی',
        'quantity' => 'تعداد',
        'amount' => 'مبلغ',
        'payment_status' => 'وضعیت پرداخت',
        'payment_method' => 'روش پرداخت',
        'total' => 'جمع کل',
        'description' => 'شرح',
        'title' => 'عنوان',
        'sku' => 'کد کالا',
        'service_type' => 'نوع سرویس',
        'serial_number' => 'شماره سریال',
        'asset_number' => 'شماره اموال',
        'deleted_at' => 'تاریخ حذف',
        'created_at' => 'تاریخ ایجاد',
        'updated_at' => 'تاریخ بروزرسانی',
        'address' => 'آدرس',
        'user_id' => 'کاربر مرتبط',
    ];

    public static function fieldLabel(string $key): string
    {
        return self::FIELD_LABELS[$key] ?? str_replace('_', ' ', $key);
    }

    public static function displayValue(string $key, mixed $value, ?string $modelType = null): string
    {
        return self::formatValue($key, $value, $modelType);
    }

    public static function eventLabel(?string $event): string
    {
        return self::EVENT_LABELS[$event ?? ''] ?? ($event ?: 'نامشخص');
    }

    public static function modelLabel(?string $type): string
    {
        $base = class_basename((string) $type);

        return self::MODEL_LABELS[$base] ?? $base;
    }

    /**
     * @return list<string>
     */
    public static function changeLines(?array $old, ?array $new, ?string $modelType = null): array
    {
        $lines = [];
        $keys = array_unique(array_merge(array_keys($old ?? []), array_keys($new ?? [])));
        $skip = ['remember_token', 'password'];

        foreach ($keys as $key) {
            if (in_array($key, $skip, true)) {
                continue;
            }

            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            if ($oldVal == $newVal) {
                continue;
            }

            $label = self::FIELD_LABELS[$key] ?? str_replace('_', ' ', $key);
            $from = self::formatValue($key, $oldVal, $modelType);
            $to = self::formatValue($key, $newVal, $modelType);

            if ($old === null && $new !== null) {
                $lines[] = "{$label}: {$to}";
            } elseif ($new === null && $old !== null) {
                $lines[] = "حذف {$label} (قبلی: {$from})";
            } else {
                $lines[] = "{$label} از «{$from}» به «{$to}» تغییر کرد";
            }
        }

        return $lines;
    }

    public static function summary(?string $event, ?string $modelType, ?int $modelId, ?array $old, ?array $new): string
    {
        $model = self::modelLabel($modelType);
        $eventLabel = self::eventLabel($event);
        $ref = $modelId ? " (#{$modelId})" : '';

        $changes = self::changeLines($old, $new, $modelType);

        if ($event === 'created') {
            return "{$eventLabel} برای {$model}{$ref}";
        }

        if ($event === 'deleted') {
            return "{$eventLabel}: {$model}{$ref}";
        }

        if ($changes === []) {
            return "{$eventLabel} در {$model}{$ref} (بدون جزئیات فیلد)";
        }

        if (count($changes) === 1) {
            return "{$eventLabel} — {$model}{$ref}: {$changes[0]}";
        }

        return "{$eventLabel} — {$model}{$ref} — " . count($changes) . ' مورد تغییر یافت';
    }

    private static function formatValue(string $key, mixed $value, ?string $modelType): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (str_ends_with($key, '_at') && is_string($value)) {
            try {
                return jalali_date(\Illuminate\Support\Carbon::parse($value), 'Y/m/d H:i');
            } catch (\Throwable) {
                return (string) $value;
            }
        }

        if ($key === 'status' && is_string($value)) {
            try {
                $enum = ServiceOrderStatus::from($value);

                return $enum->label();
            } catch (\ValueError) {
                return $value;
            }
        }

        if (in_array($key, ['debt_amount', 'service_cost', 'price', 'total', 'amount'], true) && is_numeric($value)) {
            return number_format((float) $value) . ' تومان';
        }

        if ($key === 'is_active') {
            return $value ? 'فعال' : 'غیرفعال';
        }

        if ($key === 'service_type') {
            return match ($value) {
                'in_company' => 'در محل شرکت',
                'on_site' => 'در محل مشتری',
                default => (string) $value,
            };
        }

        if ($key === 'payment_method') {
            return match ($value) {
                'cash', 'cod' => 'نقدی',
                'card' => 'کارت',
                'debt' => 'بدهی',
                'online' => 'آنلاین',
                default => (string) $value,
            };
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        if (is_bool($value)) {
            return $value ? 'بله' : 'خیر';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $str = (string) $value;

        return mb_strlen($str) > 120 ? mb_substr($str, 0, 117) . '…' : $str;
    }
}
