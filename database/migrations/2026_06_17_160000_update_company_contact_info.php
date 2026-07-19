<?php

use App\Support\CompanyProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $website = CompanyProfile::WEBSITE;
        $phone = CompanyProfile::PHONE;

        DB::table('settings')->where('key', 'print_footer_website')->update([
            'value' => $website,
            'updated_at' => now(),
        ]);

        DB::table('settings')->where('key', 'print_thermal_footer_2')->update([
            'value' => $website,
            'updated_at' => now(),
        ]);

        $this->upsertSetting('sms_business_phone', $phone, 'sms', 'تلفن تماس (در پیامک‌ها)', 'text');
        $this->upsertSetting('sms_business_address', CompanyProfile::ADDRESS, 'sms', 'آدرس (در پیامک‌ها)', 'text');

        // به‌روزرسانی مقادیر قدیمی در صورت وجود
        DB::table('settings')
            ->where('key', 'sms_business_phone')
            ->where(function ($q) {
                $q->whereNull('value')
                    ->orWhere('value', '')
                    ->orWhere('value', '066-33326961')
                    ->orWhere('value', '021-12345678');
            })
            ->update(['value' => $phone, 'updated_at' => now()]);

        DB::table('settings')
            ->whereIn('key', ['print_footer_website', 'print_thermal_footer_2'])
            ->where('value', 'www.parslian.com')
            ->update(['value' => $website, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Non-destructive rollback.
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
};
