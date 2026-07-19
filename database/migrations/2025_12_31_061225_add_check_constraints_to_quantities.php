<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add check constraints to ensure quantities are never negative
        // Using raw SQL as Laravel Schema doesn't have a direct helper for CHECK constraints yet
        
        if (config('database.default') === 'mysql' || config('database.default') === 'mariadb') {
            DB::statement('ALTER TABLE inventories ADD CONSTRAINT inventories_quantity_check CHECK (quantity >= 0)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT products_stock_quantity_check CHECK (stock_quantity >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql' || config('database.default') === 'mariadb') {
            DB::statement('ALTER TABLE inventories DROP CONSTRAINT inventories_quantity_check');
            DB::statement('ALTER TABLE products DROP CONSTRAINT products_stock_quantity_check');
        }
    }
};
