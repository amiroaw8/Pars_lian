<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('model');
            $table->string('asset_number')->nullable();
            $table->boolean('has_guarantee')->default(false);
            $table->timestamps();

            // اضافه کردن index برای جستجوی سریع
            $table->index('customer_id');
            $table->index('type');
            $table->index('model');
            $table->index('asset_number');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
