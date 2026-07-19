<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('service_order_statuses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('label');
            $table->string('color');
            $table->timestamps();
        });

        // Seed data
        DB::table('order_statuses')->insert([
            ['id' => 'pending', 'label' => 'در انتظار بررسی', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'processing', 'label' => 'در حال پردازش', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'shipped', 'label' => 'ارسال شده', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'delivered', 'label' => 'تحویل داده شده', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'cancelled', 'label' => 'لغو شده', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('payment_statuses')->insert([
            ['id' => 'pending', 'label' => 'در انتظار پرداخت', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'paid', 'label' => 'پرداخت شده', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'failed', 'label' => 'پرداخت ناموفق', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'refunded', 'label' => 'مرجوع شده', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('service_order_statuses')->insert([
            ['id' => 'registered', 'label' => 'ثبت شده', 'color' => 'blue', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'repairing', 'label' => 'در حال تعمیر', 'color' => 'yellow', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'ready', 'label' => 'آماده تحویل', 'color' => 'green', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'delivered', 'label' => 'تحویل داده شده', 'color' => 'gray', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
        Schema::dropIfExists('payment_statuses');
        Schema::dropIfExists('service_order_statuses');
    }
};
