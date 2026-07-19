<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        Setting::updateOrCreate(
            ['key' => 'two_factor_enabled'],
            [
                'value' => '1',
                'group' => 'security',
                'label' => 'فعال‌سازی تایید دو مرحله‌ای برای کارکنان',
                'type' => 'boolean',
            ]
        );
    }

    public function down(): void
    {
        Setting::where('key', 'two_factor_enabled')->delete();
    }
};
