<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // اضافه کردن فیلدهای جدید به accounting_sales
        Schema::table('accounting_sales', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('sale_date')->nullable();
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'cod', 'online'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->foreignId('order_id')->nullable()->constrained('service_orders')->onDelete('set null');

            $table->index('customer_id');
            $table->index('sale_date');
            $table->index('status');
            $table->index('order_id');
        });

        // اضافه کردن فیلدهای جدید به accounting_services
        Schema::table('accounting_services', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('service_date')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid');
            $table->decimal('tax_amount', 15, 2)->default(0);

            $table->index('technician_id');
            $table->index('service_date');
            $table->index('payment_status');
        });

        // اضافه کردن فیلدهای جدید به sms_logs
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->string('sms_id')->nullable();
            $table->boolean('api_key_set')->default(false);

            $table->index('sms_id');
            $table->index('api_key_set');
        });
    }

    public function down()
    {
        // حذف فیلدهای اضافه شده از sms_logs
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['sms_id', 'api_key_set']);
        });
    }
};
