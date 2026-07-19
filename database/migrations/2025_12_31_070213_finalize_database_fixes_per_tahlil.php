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
        // 1. Add brand_id foreign key constraint to products (if missing)
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'brand_id')) {
            $foreignKeys = Schema::getForeignKeys('products');
            $hasFK = collect($foreignKeys)->contains(fn($fk) => $fk['columns'][0] === 'brand_id');
            
            if (!$hasFK) {
                Schema::table('products', function (Blueprint $table) {
                    $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
                });
            }
        }

        // 2. Add expires_at to carts
        if (Schema::hasTable('carts') && !Schema::hasColumn('carts', 'expires_at')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->after('total');
            });
        }

        // 3. Clean up duplicate indexes in order_logs
        if (Schema::hasTable('order_logs')) {
            $indexes = Schema::getIndexes('order_logs');
            $hasDuplicate = collect($indexes)->contains(fn($index) => $index['name'] === 'order_logs_service_order_created_at_index');
            
            if ($hasDuplicate) {
                Schema::table('order_logs', function (Blueprint $table) {
                    $table->dropIndex('order_logs_service_order_created_at_index');
                });
            }
        }

        // 4. Ensure accounting_sales.order_id points to orders (Final check)
        if (Schema::hasTable('accounting_sales')) {
            $foreignKeys = Schema::getForeignKeys('accounting_sales');
            $wrongFK = collect($foreignKeys)->first(fn($fk) => $fk['columns'][0] === 'order_id' && $fk['foreign_table'] === 'service_orders');
            
            if ($wrongFK) {
                Schema::table('accounting_sales', function (Blueprint $table) {
                    $table->dropForeign(['order_id']);
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('accounting_sales')) {
            Schema::table('accounting_sales', function (Blueprint $table) {
                $table->dropForeign(['order_id']);
                $table->foreign('order_id')->references('id')->on('service_orders')->onDelete('set null');
            });
        }

        if (Schema::hasTable('order_logs')) {
            Schema::table('order_logs', function (Blueprint $table) {
                $table->index(['service_order_id', 'created_at'], 'order_logs_service_order_created_at_index');
            });
        }

        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropColumn('expires_at');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['brand_id']);
            });
        }
    }
};
