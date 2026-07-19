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
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->string('error_code')->nullable()->after('status');
            $table->text('error_message')->nullable()->after('error_code');
            $table->json('provider_response')->nullable()->after('error_message');
            $table->string('sms_id')->nullable()->change(); // اطمینان از nullable بودن
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['error_code', 'error_message', 'provider_response']);
        });
    }
};
