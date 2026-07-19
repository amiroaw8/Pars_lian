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
        // 1. Fix repair_items table
        Schema::table('repair_items', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_items', 'inventory_id')) {
                $table->foreignId('inventory_id')->nullable()->after('service_order_id')->constrained('inventories')->onDelete('set null');
            }
            if (!Schema::hasColumn('repair_items', 'cost')) {
                $table->decimal('cost', 15, 2)->default(0)->after('description');
            }
        });

        // 2. Clean up duplicate indexes in inventories
        Schema::table('inventories', function (Blueprint $table) {
            $indexes = Schema::getIndexes('inventories');
            $hasDuplicateName = collect($indexes)->contains(fn($index) => $index['name'] === 'inventory_name_index');
            
            if ($hasDuplicateName) {
                $table->dropIndex('inventory_name_index');
            }
        });

        // 3. Fix accounting_sales.order_id foreign key (if it points to service_orders wrongly)
        // Note: tahlil.txt says it points to service_orders but should point to orders.
        // We'll check the foreign key first.
        if (Schema::hasTable('accounting_sales')) {
            $foreignKeys = Schema::getForeignKeys('accounting_sales');
            $wrongFK = collect($foreignKeys)->first(fn($fk) => $fk['columns'][0] === 'order_id' && $fk['foreign_table'] === 'service_orders');
            
            if ($wrongFK) {
                Schema::table('accounting_sales', function (Blueprint $table) {
                    $table->dropForeign(['order_id']);
                    // We keep the column but it will now semantically point to 'orders' table.
                    // However, we might need to update existing data if they are incompatible.
                    // For now, let's just fix the constraint if it exists.
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
        Schema::table('accounting_sales', function (Blueprint $table) {
            $foreignKeys = Schema::getForeignKeys('accounting_sales');
            $orderFK = collect($foreignKeys)->first(fn($fk) => $fk['columns'][0] === 'order_id' && $fk['foreign_table'] === 'orders');
            
            if ($orderFK) {
                $table->dropForeign(['order_id']);
                $table->foreign('order_id')->references('id')->on('service_orders')->onDelete('set null');
            }
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->index('name', 'inventory_name_index');
        });

        Schema::table('repair_items', function (Blueprint $table) {
            if (Schema::hasColumn('repair_items', 'inventory_id')) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            }
            if (Schema::hasColumn('repair_items', 'cost')) {
                $table->dropColumn('cost');
            }
        });
    }
};
