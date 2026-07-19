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
        // 1. Unify decimal precision in orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax_amount', 15, 2)->change();
            $table->decimal('shipping_amount', 15, 2)->change();
            $table->decimal('discount_amount', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });

        // 2. Remove redundant indexes (those covered by composite indexes)
        Schema::table('service_orders', function (Blueprint $table) {
            // These are covered by [customer_id, created_at] and [status, created_at]
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            // Covered by [name, phone]
            $table->dropIndex(['name']);
            // Add link to users table if not exists
            if (!Schema::hasColumn('customers', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
        });

        Schema::table('devices', function (Blueprint $table) {
            // Covered by [type, model]
            $table->dropIndex(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->index('type');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('name');
            if (Schema::hasColumn('customers', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->change();
            $table->decimal('tax_amount', 10, 2)->change();
            $table->decimal('shipping_amount', 10, 2)->change();
            $table->decimal('discount_amount', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
        });
    }
};
