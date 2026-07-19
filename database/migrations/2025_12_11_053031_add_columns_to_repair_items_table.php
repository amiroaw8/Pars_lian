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
        Schema::table('repair_items', function (Blueprint $table) {
            $table->foreignId('service_order_id')->constrained()->onDelete('cascade');
            $table->enum('item_type', ['part', 'labor', 'other']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('cost', 15, 2);
            $table->integer('quantity')->default(1);
            $table->integer('sort_order')->default(0);

            $table->index('service_order_id');
            $table->index('item_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_items', function (Blueprint $table) {
            $table->dropForeign(['service_order_id']);
            $table->dropColumn([
                'service_order_id',
                'item_type',
                'name',
                'description',
                'cost',
                'quantity',
                'sort_order',
            ]);
        });
    }
};
