<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->boolean('sms_enabled')->default(true)->after('sms_template');
        });

        Schema::table('payment_statuses', function (Blueprint $table) {
            $table->boolean('sms_enabled')->default(true)->after('sms_template');
        });
    }

    public function down(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->dropColumn('sms_enabled');
        });

        Schema::table('payment_statuses', function (Blueprint $table) {
            $table->dropColumn('sms_enabled');
        });
    }
};
