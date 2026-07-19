<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'payment_method')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cod', 'online', 'card', 'debt', 'cash') NOT NULL DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'payment_method')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::table('orders')->where('payment_method', 'debt')->update(['payment_method' => 'cod']);
            DB::table('orders')->where('payment_method', 'cash')->update(['payment_method' => 'cod']);
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cod', 'online', 'card') NOT NULL DEFAULT 'cod'");
        }
    }
};
