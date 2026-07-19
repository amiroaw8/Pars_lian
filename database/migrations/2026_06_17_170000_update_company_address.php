<?php

use App\Support\CompanyProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_ADDRESSES = [
        'خرم آباد-میدان انوشیروان رضایی - کوچه معلم جنوبی - ساختمان مهرآیین',
        'خرم آباد - میدان انوشیروان رضایی - کوچه معلم جنوبی - ساختمان مهرآیین',
    ];

    public function up(): void
    {
        $address = CompanyProfile::ADDRESS;

        DB::table('settings')
            ->where('key', 'sms_business_address')
            ->where(function ($q) {
                $q->whereNull('value')
                    ->orWhere('value', '')
                    ->orWhereIn('value', self::OLD_ADDRESSES);
            })
            ->update(['value' => $address, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Non-destructive rollback.
    }
};
