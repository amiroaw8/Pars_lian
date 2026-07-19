<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('repair_items') || ! Schema::hasColumn('repair_items', 'name')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE repair_items MODIFY name TEXT NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('repair_items') || ! Schema::hasColumn('repair_items', 'name')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE repair_items MODIFY name VARCHAR(255) NOT NULL');
    }
};
