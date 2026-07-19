<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['device', 'part', 'tool', 'other']);
            $table->integer('quantity')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // اضافه کردن index برای جستجوی سریع
            $table->index('name');
            $table->index('type');
            $table->index('quantity');
            $table->index('price');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};
