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
        // Alter enum first to include new types - MySQL only
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN transaction_type ENUM('purchase', 'sale', 'use', 'adjustment', 'return', 'warranty_sent', 'warranty_return')");
        }

        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_transactions', 'receiver')) {
                $table->string('receiver')->nullable()->after('new_quantity')->comment('تحویل گیرنده');
            }
            if (!Schema::hasColumn('inventory_transactions', 'organization')) {
                $table->string('organization')->nullable()->after('new_quantity')->comment('ارگان'); // using new_quantity as anchor just in case receiver isn't there yet
            }
            if (!Schema::hasColumn('inventory_transactions', 'reason')) {
                $table->string('reason')->nullable()->after('new_quantity')->comment('بابت');
            }
            if (!Schema::hasColumn('inventory_transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('new_quantity')->comment('کاربر ثبت کننده');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['receiver', 'organization', 'reason']);
             // We don't drop user_id if it existed before
        });
    }
};
