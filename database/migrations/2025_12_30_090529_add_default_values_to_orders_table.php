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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('tax_amount', 12, 2)->default(0)->change();
            $table->decimal('shipping_amount', 12, 2)->default(0)->change();
            $table->decimal('discount_amount', 12, 2)->default(0)->change();
            $table->string('payment_status')->default('pending')->change();
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('tax_amount', 12, 2)->change();
            $table->decimal('shipping_amount', 12, 2)->change();
            $table->decimal('discount_amount', 12, 2)->change();
            $table->string('payment_status')->change();
            $table->string('status')->change();
        });
    }
};
