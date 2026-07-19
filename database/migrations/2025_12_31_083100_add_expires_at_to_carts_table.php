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
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->index();
            } else {
                // Just add index if column exists but not indexed
                try {
                    $table->index('expires_at');
                } catch (\Exception $e) {}
            }
            
            if (!Schema::hasColumn('carts', 'is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['is_active']);
            // We don't drop expires_at if it was already there
        });
    }
};
