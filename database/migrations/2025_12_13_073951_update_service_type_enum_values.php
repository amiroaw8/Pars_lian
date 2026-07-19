<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing records with invalid service_type values
        DB::table('service_orders')
            ->whereIn('service_type', ['repair', 'maintenance', 'upgrade', 'other'])
            ->update(['service_type' => 'in_company']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it changes data
    }
};
