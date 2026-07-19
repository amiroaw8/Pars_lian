<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('service_order_statuses')->where('id', 'technician_assigned')->update(['sms_template' => 'تکنسین {technician_name} برای بررسی دستگاه شما تعیین گردید.']);
        DB::table('service_order_statuses')->where('id', 'rejected')->update(['sms_template' => 'متاسفانه دستگاه شما قابل تعمیر نمی‌باشد. لطفا جهت تحویل مراجعه فرمایید.']);
        DB::table('service_order_statuses')->where('id', 'accounting')->update(['sms_template' => 'دستگاه شما تعمیر شده و فاکتور صادر گردید. لطفا جهت تسویه حساب اقدام نمایید. مبلغ: {cost}']);
        DB::table('service_order_statuses')->where('id', 'archived')->update(['sms_template' => 'پرونده تعمیرات شما بایگانی شد.']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('service_order_statuses')->whereIn('id', ['technician_assigned', 'rejected', 'accounting', 'archived'])->update(['sms_template' => null]);
    }
};
