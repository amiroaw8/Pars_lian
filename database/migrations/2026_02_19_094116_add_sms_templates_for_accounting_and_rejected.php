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
        DB::table('service_order_statuses')->where('id', 'accounting')->update([
            'sms_template' => 'سفارش #{id} به بخش حسابداری ارجاع شد. هزینه نهایی: {cost} تومان. لطفا جهت پرداخت و تحویل مراجعه فرمایید.'
        ]);

        DB::table('service_order_statuses')->where('id', 'rejected')->update([
            'sms_template' => 'سفارش #{id} غیرقابل تعمیر تشخیص داده شد. لطفا جهت تحویل دستگاه مراجعه فرمایید.'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for rollback as we are updating existing records
    }
};
