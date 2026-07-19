<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for ServiceOrders table
        Schema::table('service_orders', function (Blueprint $table) {
            if (! $this->indexExists('service_orders', 'service_orders_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'service_orders_status_created_at_index');
            }
            if (! $this->indexExists('service_orders', 'service_orders_customer_created_at_index')) {
                $table->index(['customer_id', 'created_at'], 'service_orders_customer_created_at_index');
            }
            if (! $this->indexExists('service_orders', 'service_orders_technician_status_index')) {
                $table->index(['technician_id', 'status'], 'service_orders_technician_status_index');
            }
            if (! $this->indexExists('service_orders', 'service_orders_service_type_index')) {
                $table->index('service_type', 'service_orders_service_type_index');
            }
            if (! $this->indexExists('service_orders', 'service_orders_repair_started_at_index')) {
                $table->index('repair_started_at', 'service_orders_repair_started_at_index');
            }
            if (! $this->indexExists('service_orders', 'service_orders_repair_completed_at_index')) {
                $table->index('repair_completed_at', 'service_orders_repair_completed_at_index');
            }
        });

        // Add indexes for Customers table
        Schema::table('customers', function (Blueprint $table) {
            if (! $this->indexExists('customers', 'customers_phone_index')) {
                $table->index('phone', 'customers_phone_index');
            }
            if (! $this->indexExists('customers', 'customers_name_phone_index')) {
                $table->index(['name', 'phone'], 'customers_name_phone_index');
            }
        });

        // Add indexes for Devices table
        Schema::table('devices', function (Blueprint $table) {
            if (! $this->indexExists('devices', 'devices_customer_id_index')) {
                $table->index('customer_id', 'devices_customer_id_index');
            }
            if (! $this->indexExists('devices', 'devices_type_model_index')) {
                $table->index(['type', 'model'], 'devices_type_model_index');
            }
            if (! $this->indexExists('devices', 'devices_asset_number_index')) {
                $table->index('asset_number', 'devices_asset_number_index');
            }
        });

        // Add indexes for Inventory table
        if (Schema::hasTable('inventories')) {
            Schema::table('inventories', function (Blueprint $table) {
                if (! $this->indexExists('inventories', 'inventory_name_index')) {
                    $table->index('name', 'inventory_name_index');
                }
                if (! $this->indexExists('inventories', 'inventory_type_quantity_index')) {
                    $table->index(['type', 'quantity'], 'inventory_type_quantity_index');
                }
            });
        }

        // Add indexes for InventoryTransactions table
        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (! $this->indexExists('inventory_transactions', 'inventory_transactions_inventory_created_at_index')) {
                $table->index(['inventory_id', 'created_at'], 'inventory_transactions_inventory_created_at_index');
            }
            if (! $this->indexExists('inventory_transactions', 'inventory_transactions_type_index')) {
                $table->index('transaction_type', 'inventory_transactions_type_index');
            }
        });

        // Add indexes for SMSLogs table
        Schema::table('sms_logs', function (Blueprint $table) {
            if (! $this->indexExists('sms_logs', 'sms_logs_service_order_created_at_index')) {
                $table->index(['service_order_id', 'created_at'], 'sms_logs_service_order_created_at_index');
            }
            if (! $this->indexExists('sms_logs', 'sms_logs_status_index')) {
                $table->index('status', 'sms_logs_status_index');
            }
        });

        // Add indexes for OrderLogs table
        Schema::table('order_logs', function (Blueprint $table) {
            if (! $this->indexExists('order_logs', 'order_logs_service_order_created_at_index')) {
                $table->index(['service_order_id', 'created_at'], 'order_logs_service_order_created_at_index');
            }
            if (! $this->indexExists('order_logs', 'order_logs_user_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'order_logs_user_created_at_index');
            }
        });

        // Add indexes for Accounting tables
        Schema::table('accounting_sales', function (Blueprint $table) {
            if (! $this->indexExists('accounting_sales', 'accounting_sales_customer_date_index')) {
                $table->index(['customer_id', 'sale_date'], 'accounting_sales_customer_date_index');
            }
            if (! $this->indexExists('accounting_sales', 'accounting_sales_date_index')) {
                $table->index('sale_date', 'accounting_sales_date_index');
            }
        });

        // Add indexes for Accounting services table (only if columns exist)
        Schema::table('accounting_services', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_services', 'service_order_id') &&
                ! $this->indexExists('accounting_services', 'accounting_services_order_date_index')) {
                $table->index(['service_order_id', 'service_date'], 'accounting_services_order_date_index');
            }
            if (Schema::hasColumn('accounting_services', 'technician_id') &&
                ! $this->indexExists('accounting_services', 'accounting_services_technician_date_index')) {
                $table->index(['technician_id', 'service_date'], 'accounting_services_technician_date_index');
            }
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");
            foreach ($indexes as $index) {
                if ($index->name === $indexName) {
                    return true;
                }
            }
            return false;
        }

        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");

        return ! empty($indexes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for ServiceOrders table
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex('service_orders_status_created_at_index');
            $table->dropIndex('service_orders_customer_created_at_index');
            $table->dropIndex('service_orders_technician_status_index');
            $table->dropIndex('service_orders_service_type_index');
            $table->dropIndex('service_orders_repair_started_at_index');
            $table->dropIndex('service_orders_repair_completed_at_index');
        });

        // Drop indexes for Customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_phone_index');
            $table->dropIndex('customers_name_phone_index');
        });

        // Drop indexes for Devices table
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex('devices_customer_id_index');
            $table->dropIndex('devices_type_model_index');
            $table->dropIndex('devices_asset_number_index');
        });

        // Drop indexes for Inventory table
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('inventory_name_index');
            $table->dropIndex('inventory_type_quantity_index');
        });

        // Drop indexes for InventoryTransactions table
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('inventory_transactions_inventory_created_at_index');
            $table->dropIndex('inventory_transactions_type_index');
        });

        // Drop indexes for SMSLogs table
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropIndex('sms_logs_service_order_created_at_index');
            $table->dropIndex('sms_logs_status_index');
        });

        // Drop indexes for OrderLogs table
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropIndex('order_logs_service_order_created_at_index');
            $table->dropIndex('order_logs_user_created_at_index');
        });

        // Drop indexes for Accounting tables
        Schema::table('accounting_sales', function (Blueprint $table) {
            $table->dropIndex('accounting_sales_customer_date_index');
            $table->dropIndex('accounting_sales_date_index');
        });

        Schema::table('accounting_services', function (Blueprint $table) {
            $table->dropIndex('accounting_services_order_date_index');
            $table->dropIndex('accounting_services_technician_date_index');
        });
    }
};
