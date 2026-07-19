<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('inventory_transactions')
            ->whereRaw('UPPER(transaction_type) = ?', ['RETURN'])
            ->update(['transaction_type' => 'return']);
    }

    public function down(): void
    {
        // irreversible data normalization
    }
};
