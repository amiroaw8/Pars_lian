<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_order_statuses', function (Blueprint $table) {
            $table->string('sms_template')->nullable()->after('color');
        });

        // Update existing data
        DB::table('service_order_statuses')->where('id', 'registered')->update(['sms_template' => 'دستگاه شما با موفقیت ثبت شد. کد پیگیری: {id}']);
        DB::table('service_order_statuses')->where('id', 'repairing')->update(['sms_template' => 'دستگاه شما در حال تعمیر می‌باشد.']);
        DB::table('service_order_statuses')->where('id', 'ready')->update(['sms_template' => 'دستگاه شما آماده تحویل است.']);
        DB::table('service_order_statuses')->where('id', 'delivered')->update(['sms_template' => 'دستگاه شما تحویل داده شد. با تشکر']);
    }

    public function down(): void
    {
        Schema::table('service_order_statuses', function (Blueprint $table) {
            $table->dropColumn('sms_template');
        });
    }
};
