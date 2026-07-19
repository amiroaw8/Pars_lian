<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $serviceStatuses = [
            'registered' => [
                'sms_template' => 'سفارش #{id} در سیستم ثبت شد. پارس لیان',
                'sms_enabled' => false,
            ],
            'technician_assigned' => [
                'sms_template' => 'سفارش #{id}: تکنسین {technician_name} برای بررسی تعیین شد. پارس لیان',
                'sms_enabled' => true,
            ],
            'repairing' => [
                'sms_template' => 'سفارش #{id} ({device}) در حال تعمیر است. پارس لیان',
                'sms_enabled' => true,
            ],
            'ready' => [
                'sms_template' => 'سفارش #{id} آماده تحویل است. لطفاً برای دریافت مراجعه فرمایید. پارس لیان',
                'sms_enabled' => true,
            ],
            'accounting' => [
                'sms_template' => 'سفارش #{id}: فاکتور {cost} تومان صادر شد. برای تسویه مراجعه کنید. پارس لیان',
                'sms_enabled' => true,
            ],
            'rejected' => [
                'sms_template' => 'سفارش #{id}: متأسفانه قابل تعمیر نیست. برای تحویل دستگاه تماس بگیرید. پارس لیان',
                'sms_enabled' => true,
            ],
            'delivered' => [
                'sms_template' => 'سفارش #{id} تحویل شد. ممنون از اعتماد شما — پارس لیان',
                'sms_enabled' => true,
            ],
            'archived' => [
                'sms_template' => null,
                'sms_enabled' => false,
            ],
        ];

        foreach ($serviceStatuses as $id => $data) {
            $update = ['sms_enabled' => $data['sms_enabled']];
            if ($data['sms_template'] !== null) {
                $update['sms_template'] = $data['sms_template'];
            }
            DB::table('service_order_statuses')->where('id', $id)->update($update);
        }

        $shopOrderStatuses = [
            'pending' => 'سفارش {id} ثبت شد و در انتظار بررسی است. پارس لیان',
            'processing' => 'سفارش {id} در حال آماده‌سازی است. پارس لیان',
            'shipped' => 'سفارش {id} ارسال شد. رهگیری: {tracking_code} — پارس لیان',
            'delivered' => 'سفارش {id} تحویل شد. از خرید شما سپاسگزاریم. پارس لیان',
        ];

        foreach ($shopOrderStatuses as $id => $template) {
            DB::table('order_statuses')->where('id', $id)->update(['sms_template' => $template]);
        }

        $shopPaymentStatuses = [
            'paid' => 'پرداخت سفارش {id} با موفقیت انجام شد. پارس لیان',
            'failed' => 'پرداخت سفارش {id} ناموفق بود. لطفاً دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.',
        ];

        foreach ($shopPaymentStatuses as $id => $template) {
            DB::table('payment_statuses')->where('id', $id)->update(['sms_template' => $template]);
        }

        $this->upsertSetting('sms_business_phone', '066-33308603', 'sms', 'تلفن تماس (در پیامک‌ها)', 'text');
        $this->upsertSetting('sms_business_address', 'خرم آباد - شهدای شرقی - خیابان معلم - کوچه رنگین کمان جنوبی - ساختمان مهرآیین', 'sms', 'آدرس (در پیامک‌ها)', 'text');

        $this->updateSettingIfEmptyOrLegacy('sms_template_order_registered', [
            'مشتری گرامی {customer_name}، سفارش تعمیر شما با شماره {id} برای دستگاه {device} ثبت شد. پارس لیان',
        ], "{customer_name} عزیز،\nدستگاه {device} ثبت شد.\nکد پیگیری: {id}\nپارس لیان");

        $this->updateSettingIfEmptyOrLegacy('sms_template_debt_notification', [
            'مشتری گرامی {customer_name}، تعمیر دستگاه شما (سفارش #{id}) انجام شد. مبلغ {cost} تومان به عنوان بدهی ثبت گردید. لطفاً جهت تسویه و دریافت دستگاه به پارس لیان مراجعه فرمایید.',
        ], "{customer_name} عزیز،\nتعمیر سفارش #{id} انجام شد.\nمبلغ قابل پرداخت: {cost} تومان\nبرای تحویل، لطفاً مراجعه یا تماس بگیرید.\nپارس لیان");

        $this->updateSettingIfEmptyOrLegacy('sms_template_two_factor', [
            'کد تایید دو مرحله‌ای شما: {code}',
        ], "پارس لیان\nکد ورود: {code}\nاعتبار: ۲ دقیقه\nاین کد را به کسی ندهید.");

        $this->updateSettingIfEmptyOrLegacy('sms_template_password_reset', [
            "کد بازیابی رمز عبور شما: {code}\nاین کد ۱۵ دقیقه معتبر است.",
        ], "پارس لیان\nکد بازیابی رمز: {code}\nاعتبار: ۱۵ دقیقه\nاگر درخواست ندادید، این پیام را نادیده بگیرید.");

        $this->updateSettingIfEmptyOrLegacy('sms_template_inventory_item', [
            "هشدار موجودی: کالای {product_name} به حداقل موجودی رسید.\nموجودی فعلی: {quantity}",
        ], "⚠️ موجودی کم\n{product_name}\nموجودی: {quantity} (حداقل: {min_quantity})\nپارس لیان — پنل انبار");

        $this->updateSettingIfEmptyOrLegacy('sms_template_inventory_batch', [
            "هشدار موجودی انبار (پارس لیان)\nتعداد {count} کالا به حداقل موجودی رسیده‌اند:\n{items_list}\nلطفا پنل مدیریت را بررسی کنید.",
        ], "⚠️ {count} کالا کم‌موجود\n{items_list}\nپنل مدیریت را بررسی کنید.");
    }

    public function down(): void
    {
        // Non-destructive: improved templates are not reverted automatically.
    }

    private function upsertSetting(string $key, string $value, string $group, string $label, string $type): void
    {
        if (DB::table('settings')->where('key', $key)->exists()) {
            return;
        }

        DB::table('settings')->insert([
            'key' => $key,
            'value' => $value,
            'group' => $group,
            'label' => $label,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @param  array<int, string>  $legacyValues */
    private function updateSettingIfEmptyOrLegacy(string $key, array $legacyValues, string $newValue): void
    {
        $row = DB::table('settings')->where('key', $key)->first();

        if (! $row) {
            DB::table('settings')->insert([
                'key' => $key,
                'value' => $newValue,
                'group' => 'sms',
                'label' => $key,
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        $current = trim((string) ($row->value ?? ''));

        if ($current === '' || in_array($current, $legacyValues, true)) {
            DB::table('settings')->where('key', $key)->update([
                'value' => $newValue,
                'updated_at' => now(),
            ]);
        }
    }
};
