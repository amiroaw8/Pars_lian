<?php

namespace App\Support;

use App\Models\Inventory;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SmsNotifications
{
    public static function isStatusEnabled(string $statusId): bool
    {
        $row = DB::table('service_order_statuses')->where('id', $statusId)->first();

        return $row && (bool) ($row->sms_enabled ?? true);
    }

    public static function isOrderRegisteredEnabled(): bool
    {
        return Setting::get('sms_order_registered', '1') === '1';
    }

    public static function isDebtNotificationEnabled(): bool
    {
        return Setting::get('sms_debt_notification', '1') === '1';
    }

    public static function isTwoFactorEnabled(): bool
    {
        return Setting::get('two_factor_enabled', '1') === '1';
    }

    public static function isPasswordResetEnabled(): bool
    {
        return Setting::get('sms_password_reset', '1') === '1';
    }

    public static function isInventoryAlertEnabled(): bool
    {
        return Setting::get('sms_inventory_alert', '1') === '1';
    }

    public static function isShopOrderStatusEnabled(string $statusId): bool
    {
        $row = DB::table('order_statuses')->where('id', $statusId)->first();

        return $row && (bool) ($row->sms_enabled ?? true);
    }

    public static function isShopPaymentStatusEnabled(string $statusId): bool
    {
        $row = DB::table('payment_statuses')->where('id', $statusId)->first();

        return $row && (bool) ($row->sms_enabled ?? true);
    }

    public static function orderRegisteredTemplate(): string
    {
        $custom = trim((string) Setting::get('sms_template_order_registered', ''));

        if ($custom !== '') {
            return $custom;
        }

        $fromStatus = DB::table('service_order_statuses')->where('id', 'registered')->value('sms_template');

        return trim((string) $fromStatus) ?: self::defaultOrderRegisteredTemplate();
    }

    public static function debtNotificationTemplate(): string
    {
        $custom = trim((string) Setting::get('sms_template_debt_notification', ''));

        return $custom !== '' ? $custom : self::defaultDebtNotificationTemplate();
    }

    public static function twoFactorTemplate(): string
    {
        $custom = trim((string) Setting::get('sms_template_two_factor', ''));

        return $custom !== '' ? $custom : self::defaultTwoFactorTemplate();
    }

    public static function passwordResetTemplate(): string
    {
        $custom = trim((string) Setting::get('sms_template_password_reset', ''));

        return $custom !== '' ? $custom : self::defaultPasswordResetTemplate();
    }

    public static function inventoryItemAlertTemplate(): string
    {
        $custom = trim((string) Setting::get('sms_template_inventory_item', ''));

        return $custom !== '' ? $custom : self::defaultInventoryItemAlertTemplate();
    }

    public static function inventoryBatchAlertTemplate(): string
    {
        $custom = trim((string) Setting::get('sms_template_inventory_batch', ''));

        return $custom !== '' ? $custom : self::defaultInventoryBatchAlertTemplate();
    }

    public static function defaultOrderRegisteredTemplate(): string
    {
        return "{customer_name} عزیز،\nدستگاه {device} ثبت شد.\nکد پیگیری: {id}\nپارس لیان";
    }

    public static function defaultDebtNotificationTemplate(): string
    {
        return "{customer_name} عزیز،\nتعمیر سفارش #{id} انجام شد.\nمبلغ قابل پرداخت: {cost} تومان\nتماس: {phone}\nپارس لیان";
    }

    public static function defaultTwoFactorTemplate(): string
    {
        return "پارس لیان\nکد ورود: {code}\nاعتبار: ۲ دقیقه\nاین کد را به کسی ندهید.";
    }

    public static function defaultPasswordResetTemplate(): string
    {
        return "پارس لیان\nکد بازیابی رمز: {code}\nاعتبار: ۱۵ دقیقه\nاگر درخواست ندادید، این پیام را نادیده بگیرید.";
    }

    public static function defaultInventoryItemAlertTemplate(): string
    {
        return "⚠️ موجودی کم\n{product_name}\nموجودی: {quantity} (حداقل: {min_quantity})\nپارس لیان — پنل انبار";
    }

    public static function defaultInventoryBatchAlertTemplate(): string
    {
        return "⚠️ {count} کالا کم‌موجود\n{items_list}\nپنل مدیریت را بررسی کنید.";
    }

    /** @return array<string, string> */
    public static function businessPlaceholders(): array
    {
        $phone = trim((string) Setting::get('sms_business_phone', ''));
        $address = trim((string) Setting::get('sms_business_address', ''));

        return [
            '{phone}' => $phone !== '' ? $phone : CompanyProfile::PHONE,
            '{address}' => $address !== '' ? $address : CompanyProfile::ADDRESS,
            '{website}' => CompanyProfile::WEBSITE,
        ];
    }

    public static function prepareTwoFactorMessage(string $code): string
    {
        return self::applyTemplate(self::twoFactorTemplate(), ['{code}' => $code]);
    }

    public static function preparePasswordResetMessage(string $code): string
    {
        return self::applyTemplate(self::passwordResetTemplate(), ['{code}' => $code]);
    }

    public static function prepareInventoryItemAlertMessage(Inventory $inventory): string
    {
        $name = $inventory->name ?? $inventory->title ?? '—';

        return self::applyTemplate(self::inventoryItemAlertTemplate(), [
            '{product_name}' => $name,
            '{quantity}' => (string) $inventory->quantity,
            '{min_quantity}' => (string) ($inventory->min_quantity ?? ''),
        ]);
    }

    /**
     * @param  Collection<int, Inventory>  $items
     */
    public static function prepareInventoryBatchAlertMessage(Collection $items): string
    {
        $count = $items->count();
        $lines = $items->take(5)->map(function (Inventory $item) {
            $name = $item->name ?? $item->title ?? '—';

            return "- {$name}: {$item->quantity}";
        })->implode("\n");

        if ($count > 5) {
            $lines .= "\n... و ".($count - 5).' مورد دیگر.';
        }

        return self::applyTemplate(self::inventoryBatchAlertTemplate(), [
            '{count}' => (string) $count,
            '{items_list}' => $lines,
        ]);
    }

    /** @return array<string, string> */
    public static function applyTemplate(string $template, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * دسته‌بندی پیامک‌های قابل تنظیم در پنل (برای UI).
     *
     * @return array<string, array{label: string, icon: string, description: string, variables: array<int, array{token: string, label: string}>}>
     */
    public static function categories(): array
    {
        return [
            'security' => [
                'label' => 'امنیت و ورود',
                'icon' => 'shield-lock',
                'description' => 'پیامک‌های مربوط به ورود کارکنان و بازیابی رمز عبور',
                'variables' => [
                    ['token' => '{code}', 'label' => 'کد ۶ رقمی'],
                ],
            ],
            'service_special' => [
                'label' => 'سفارش تعمیر — رویدادهای ویژه',
                'icon' => 'tool',
                'description' => 'پیامک‌هایی که مستقل از تغییر وضعیت ارسال می‌شوند',
                'variables' => [
                    ['token' => '{id}', 'label' => 'شماره سفارش'],
                    ['token' => '{customer_name}', 'label' => 'نام مشتری'],
                    ['token' => '{device}', 'label' => 'مدل دستگاه'],
                    ['token' => '{cost}', 'label' => 'مبلغ بدهی'],
                    ['token' => '{phone}', 'label' => 'تلفن تماس'],
                    ['token' => '{address}', 'label' => 'آدرس'],
                ],
            ],
            'service_status' => [
                'label' => 'سفارش تعمیر — تغییر وضعیت',
                'icon' => 'refresh',
                'description' => 'هنگام تغییر وضعیت سفارش تعمیر برای مشتری ارسال می‌شود',
                'variables' => [
                    ['token' => '{id}', 'label' => 'شماره سفارش'],
                    ['token' => '{customer_name}', 'label' => 'نام مشتری'],
                    ['token' => '{device}', 'label' => 'مدل دستگاه'],
                    ['token' => '{cost}', 'label' => 'هزینه نهایی'],
                    ['token' => '{technician_name}', 'label' => 'نام تکنسین'],
                    ['token' => '{phone}', 'label' => 'تلفن تماس'],
                    ['token' => '{address}', 'label' => 'آدرس'],
                ],
            ],
            'shop_order' => [
                'label' => 'فروشگاه آنلاین — وضعیت سفارش',
                'icon' => 'shopping-cart',
                'description' => 'پیامک تغییر وضعیت سفارش فروشگاه برای مشتری',
                'variables' => [
                    ['token' => '{id}', 'label' => 'شماره سفارش'],
                    ['token' => '{tracking_code}', 'label' => 'کد رهگیری'],
                ],
            ],
            'shop_payment' => [
                'label' => 'فروشگاه آنلاین — وضعیت پرداخت',
                'icon' => 'credit-card',
                'description' => 'پیامک تغییر وضعیت پرداخت سفارش فروشگاه',
                'variables' => [
                    ['token' => '{id}', 'label' => 'شماره سفارش'],
                ],
            ],
            'warehouse' => [
                'label' => 'انبار — هشدار موجودی',
                'icon' => 'package',
                'description' => 'پیامک هشدار به مدیران انبار و ادمین',
                'variables' => [
                    ['token' => '{product_name}', 'label' => 'نام کالا'],
                    ['token' => '{quantity}', 'label' => 'موجودی فعلی'],
                    ['token' => '{min_quantity}', 'label' => 'حداقل موجودی'],
                    ['token' => '{count}', 'label' => 'تعداد کالاهای کم‌موجود'],
                    ['token' => '{items_list}', 'label' => 'لیست کالاها'],
                ],
            ],
        ];
    }
}
