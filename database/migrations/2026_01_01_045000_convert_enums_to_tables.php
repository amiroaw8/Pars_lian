<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convert orders table status columns
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
            $table->string('payment_status', 50)->default('pending')->change();
            
            $table->foreign('status')->references('id')->on('order_statuses');
            $table->foreign('payment_status')->references('id')->on('payment_statuses');
        });

        // 2. Convert service_orders table status column
        Schema::table('service_orders', function (Blueprint $table) {
            $table->string('status', 50)->default('registered')->change();
            
            $table->foreign('status')->references('id')->on('service_order_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['status']);
            $table->dropForeign(['payment_status']);
            
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending')->change();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->change();
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['status']);
            
            $table->enum('status', ['registered', 'repairing', 'ready', 'delivered'])->default('registered')->change();
        });
    }
};
