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
        Schema::table('service_orders', function (Blueprint $table) {
            // Add debt_amount column for recording customer debt before delivery
            $table->decimal('debt_amount', 15, 2)->default(0)->after('service_cost');
            $table->text('debt_reason')->nullable()->after('debt_amount');
            $table->index('debt_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex('service_orders_debt_amount_index');
            $table->dropColumn(['debt_amount', 'debt_reason']);
        });
    }
};
