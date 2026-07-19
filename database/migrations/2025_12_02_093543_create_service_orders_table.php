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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'repairing', 'ready', 'delivered'])->default('registered');
            $table->enum('service_type', ['in_company', 'on_site']);
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('user_department')->nullable();
            $table->text('accessories')->nullable();
            $table->text('fault');
            $table->text('notes')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->text('repair_steps')->nullable();
            $table->text('used_parts')->nullable();
            $table->decimal('service_cost', 15, 2)->default(0);
            $table->timestamp('repair_started_at')->nullable();
            $table->timestamp('repair_completed_at')->nullable();
            $table->timestamps();

            // اضافه کردن index برای جستجوی سریع
            $table->index('customer_id');
            $table->index('device_id');
            $table->index('status');
            $table->index('service_type');
            $table->index('receiver_phone');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
