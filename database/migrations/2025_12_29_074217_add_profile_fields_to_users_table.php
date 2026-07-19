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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('province')->nullable()->after('phone');
            $table->string('city')->nullable()->after('province');
            $table->string('street')->nullable()->after('city');
            $table->string('alley')->nullable()->after('street');
            $table->string('plate')->nullable()->after('alley');
            $table->string('postal_code')->nullable()->after('plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'province',
                'city',
                'street',
                'alley',
                'plate',
                'postal_code',
            ]);
        });
    }
};
