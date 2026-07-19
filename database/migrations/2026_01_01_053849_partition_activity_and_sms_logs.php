<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL/MariaDB partitioning requires the partition key to be part of the primary key.
        // We add created_at to the primary key.
        
        try {
            // Activity Logs Partitioning
            DB::statement("ALTER TABLE activity_logs DROP PRIMARY KEY, ADD PRIMARY KEY (id, created_at)");
            DB::statement("ALTER TABLE activity_logs PARTITION BY RANGE (YEAR(created_at)) (
                PARTITION p2025 VALUES LESS THAN (2026),
                PARTITION p2026 VALUES LESS THAN (2027),
                PARTITION p2027 VALUES LESS THAN (2028),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            )");

            // SMS Logs Partitioning
            DB::statement("ALTER TABLE sms_logs DROP PRIMARY KEY, ADD PRIMARY KEY (id, created_at)");
            DB::statement("ALTER TABLE sms_logs PARTITION BY RANGE (YEAR(created_at)) (
                PARTITION p2025 VALUES LESS THAN (2026),
                PARTITION p2026 VALUES LESS THAN (2027),
                PARTITION p2027 VALUES LESS THAN (2028),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            )");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Partitioning failed: " . $e->getMessage());
            // If partitioning fails, we don't want to stop the migration process 
            // as some DB engines might not support it.
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE activity_logs REMOVE PARTITIONING");
            DB::statement("ALTER TABLE activity_logs DROP PRIMARY KEY, ADD PRIMARY KEY (id)");
            
            DB::statement("ALTER TABLE sms_logs REMOVE PARTITIONING");
            DB::statement("ALTER TABLE sms_logs DROP PRIMARY KEY, ADD PRIMARY KEY (id)");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Reversing partitioning failed: " . $e->getMessage());
        }
    }
};
