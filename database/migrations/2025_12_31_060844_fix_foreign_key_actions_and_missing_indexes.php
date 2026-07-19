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
        // 1. Fix service_orders technician_id foreign key
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['technician_id']);
            $table->foreign('technician_id')->references('id')->on('users')->onDelete('set null');
            
            // Add performance index
            $table->index(['status', 'technician_id', 'created_at'], 'so_status_tech_created_idx');
        });

        // 2. Fix inventory_transactions user_id foreign key
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Add missing index to orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('orders_user_status_created_idx');
            });
        }

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex('so_status_tech_created_idx');
            $table->dropForeign(['technician_id']);
            $table->foreign('technician_id')->references('id')->on('users');
        });
    }
};
