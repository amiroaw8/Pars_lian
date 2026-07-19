<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounting_sales') || ! Schema::hasColumn('accounting_sales', 'payment_method')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE accounting_sales MODIFY payment_method ENUM('cash', 'card', 'bank_transfer', 'cod', 'online', 'debt') NOT NULL DEFAULT 'cash'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('accounting_sales') || ! Schema::hasColumn('accounting_sales', 'payment_method')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::table('accounting_sales')->where('payment_method', 'debt')->update(['payment_method' => 'cash']);
            DB::statement("ALTER TABLE accounting_sales MODIFY payment_method ENUM('cash', 'card', 'bank_transfer', 'cod', 'online') NOT NULL DEFAULT 'cash'");
        }
    }
};
