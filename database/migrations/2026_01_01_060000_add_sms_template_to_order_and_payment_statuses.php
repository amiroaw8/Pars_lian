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
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->string('sms_template')->nullable()->after('label');
        });

        Schema::table('payment_statuses', function (Blueprint $table) {
            $table->string('sms_template')->nullable()->after('label');
        });

        // Seed initial templates for Order Statuses
        DB::table('order_statuses')->where('id', 'pending')->update(['sms_template' => 'سفارش شما ثبت شد و در انتظار تایید است. شماره سفارش: {id}']);
        DB::table('order_statuses')->where('id', 'processing')->update(['sms_template' => 'سفارش شما تایید شد و در حال آماده‌سازی است.']);
        DB::table('order_statuses')->where('id', 'shipped')->update(['sms_template' => 'سفارش شما ارسال شد. کد رهگیری: {tracking_code}']);
        DB::table('order_statuses')->where('id', 'delivered')->update(['sms_template' => 'سفارش شما با موفقیت تحویل داده شد.']);

        // Seed initial templates for Payment Statuses
        DB::table('payment_statuses')->where('id', 'paid')->update(['sms_template' => 'پرداخت سفارش {id} با موفقیت انجام شد.']);
        DB::table('payment_statuses')->where('id', 'failed')->update(['sms_template' => 'پرداخت سفارش {id} ناموفق بود.']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->dropColumn('sms_template');
        });

        Schema::table('payment_statuses', function (Blueprint $table) {
            $table->dropColumn('sms_template');
        });
    }
};
