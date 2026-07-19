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
        // 1. Update inventories table with new tracking fields
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('inventories', 'device_code')) {
                $table->string('device_code')->nullable()->after('sku')->comment('Code for device compatibility or tracking');
            }
            if (!Schema::hasColumn('inventories', 'rack_location')) {
                $table->string('rack_location')->nullable()->after('device_code');
            }
            if (!Schema::hasColumn('inventories', 'compatibility_notes')) {
                $table->text('compatibility_notes')->nullable()->after('description');
            }
        });

        // 2. Link products to inventory
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'inventory_id')) {
                $table->foreignId('inventory_id')->nullable()->after('id')->constrained('inventories')->nullOnDelete();
            }
        });

        // 3. Link repair_items to inventory
        Schema::table('repair_items', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_items', 'inventory_id')) {
                $table->foreignId('inventory_id')->nullable()->after('service_order_id')->constrained('inventories')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_items', function (Blueprint $table) {
            if (Schema::hasColumn('repair_items', 'inventory_id')) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'inventory_id')) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            }
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['sku', 'device_code', 'rack_location', 'compatibility_notes']);
        });
    }
};
