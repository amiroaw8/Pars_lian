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
        DB::table('service_order_statuses')->where('id', 'ready')->update(['sms_template' => 'پرداخت شما تایید شد. دستگاه آماده تحویل است. لطفا جهت دریافت مراجعه فرمایید.']);
        DB::table('service_order_statuses')->where('id', 'delivered')->update(['sms_template' => 'دستگاه تحویل شما گردید. با تشکر از انتخاب شما.']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('service_order_statuses')->whereIn('id', ['ready', 'delivered'])->update(['sms_template' => null]);
    }
};
