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
        // Add indexes to activity_logs
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $indexes = Schema::getIndexes('activity_logs');
                $indexNames = collect($indexes)->pluck('name')->toArray();
                
                if (!in_array('activity_logs_loggable_index', $indexNames)) {
                    $table->index(['loggable_type', 'loggable_id'], 'activity_logs_loggable_index');
                }
                if (!in_array('activity_logs_user_id_index', $indexNames)) {
                    $table->index('user_id', 'activity_logs_user_id_index');
                }
                if (!in_array('activity_logs_event_index', $indexNames)) {
                    $table->index('event', 'activity_logs_event_index');
                }
            });
        }

        // Add indexes and constraints to sms_logs
        if (Schema::hasTable('sms_logs')) {
            Schema::table('sms_logs', function (Blueprint $table) {
                $indexes = Schema::getIndexes('sms_logs');
                $indexNames = collect($indexes)->pluck('name')->toArray();

                if (!in_array('sms_logs_phone_index', $indexNames)) {
                    $table->index('phone', 'sms_logs_phone_index');
                }
                if (!in_array('sms_logs_status_index', $indexNames)) {
                    $table->index('status', 'sms_logs_status_index');
                }
                if (!in_array('sms_logs_service_order_id_index', $indexNames)) {
                    $table->index('service_order_id', 'sms_logs_service_order_id_index');
                }
            });
        }

        // Add indexes to order_logs
        if (Schema::hasTable('order_logs')) {
            Schema::table('order_logs', function (Blueprint $table) {
                $indexes = Schema::getIndexes('order_logs');
                $indexNames = collect($indexes)->pluck('name')->toArray();

                if (!in_array('order_logs_service_order_id_index', $indexNames)) {
                    $table->index('service_order_id', 'order_logs_service_order_id_index');
                }
                if (!in_array('order_logs_user_id_index', $indexNames)) {
                    $table->index('user_id', 'order_logs_user_id_index');
                }
                if (!in_array('order_logs_action_index', $indexNames)) {
                    $table->index('action', 'order_logs_action_index');
                }
            });
        }

        // Add indexes to attachments
        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                $indexes = Schema::getIndexes('attachments');
                $indexNames = collect($indexes)->pluck('name')->toArray();

                if (!in_array('attachments_attachable_index', $indexNames)) {
                    $table->index(['attachable_type', 'attachable_id'], 'attachments_attachable_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_loggable_index');
            $table->dropIndex('activity_logs_user_id_index');
            $table->dropIndex('activity_logs_event_index');
        });

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropIndex('sms_logs_phone_index');
            $table->dropIndex('sms_logs_status_index');
            $table->dropIndex('sms_logs_service_order_id_index');
        });

        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropIndex('order_logs_service_order_id_index');
            $table->dropIndex('order_logs_user_id_index');
            $table->dropIndex('order_logs_action_index');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropIndex('attachments_attachable_index');
        });
    }
};
