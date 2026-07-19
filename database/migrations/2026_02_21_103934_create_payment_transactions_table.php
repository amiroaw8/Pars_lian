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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 0); // Iranian Rial
            $table->string('transaction_id')->nullable(); // Authority
            $table->string('reference_id')->nullable(); // Bank Reference
            $table->string('gateway'); // zarinpal, mellat, etc.
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->text('description')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            
            $table->index(['transaction_id', 'gateway']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
