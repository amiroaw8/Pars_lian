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
        DB::table('service_order_statuses')->insert([
            ['id' => 'technician_assigned', 'label' => 'تعیین تکنسین', 'color' => 'indigo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'rejected', 'label' => 'غیر قابل تعمیر', 'color' => 'red', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'accounting', 'label' => 'در انتظار حسابداری', 'color' => 'orange', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'archived', 'label' => 'بایگانی شده', 'color' => 'gray', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('service_order_statuses')->where('id', 'delivered')->update(['color' => 'teal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('service_order_statuses')->whereIn('id', ['technician_assigned', 'rejected', 'accounting', 'archived'])->delete();
        DB::table('service_order_statuses')->where('id', 'delivered')->update(['color' => 'gray']);
    }
};
