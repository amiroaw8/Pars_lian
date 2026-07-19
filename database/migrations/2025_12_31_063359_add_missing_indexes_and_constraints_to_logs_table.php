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
        // Add unique constraint to attachments if name and path should be unique together
        // Though path is usually enough. Let's ensure path is indexed.
        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                $indexes = Schema::getIndexes('attachments');
                $indexNames = collect($indexes)->pluck('name')->toArray();
                
                if (!in_array('attachments_path_index', $indexNames)) {
                    $table->index('path', 'attachments_path_index');
                }
            });
        }

        // Add index to inventory for common search fields
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                $indexes = Schema::getIndexes('inventory');
                $indexNames = collect($indexes)->pluck('name')->toArray();

                if (!in_array('inventory_type_index', $indexNames)) {
                    $table->index('type', 'inventory_type_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                $table->dropIndex('attachments_path_index');
            });
        }

        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropIndex('inventory_type_index');
            });
        }
    }
};
