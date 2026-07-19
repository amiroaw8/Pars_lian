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
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // 1. Trigger for orders total consistency (BEFORE INSERT)
        DB::unprepared("
            CREATE TRIGGER trg_orders_total_insert BEFORE INSERT ON orders
            FOR EACH ROW
            BEGIN
                SET NEW.total = NEW.subtotal + NEW.tax_amount + NEW.shipping_amount - NEW.discount_amount;
            END
        ");

        // 2. Trigger for orders total consistency (BEFORE UPDATE)
        DB::unprepared("
            CREATE TRIGGER trg_orders_total_update BEFORE UPDATE ON orders
            FOR EACH ROW
            BEGIN
                SET NEW.total = NEW.subtotal + NEW.tax_amount + NEW.shipping_amount - NEW.discount_amount;
            END
        ");

        // 3. Trigger for service_orders cost consistency (AFTER INSERT repair_items)
        DB::unprepared("
            CREATE TRIGGER trg_repair_items_cost_insert AFTER INSERT ON repair_items
            FOR EACH ROW
            BEGIN
                UPDATE service_orders 
                SET service_cost = (SELECT IFNULL(SUM(cost * quantity), 0) FROM repair_items WHERE service_order_id = NEW.service_order_id)
                WHERE id = NEW.service_order_id;
            END
        ");

        // 4. Trigger for service_orders cost consistency (AFTER UPDATE repair_items)
        DB::unprepared("
            CREATE TRIGGER trg_repair_items_cost_update AFTER UPDATE ON repair_items
            FOR EACH ROW
            BEGIN
                UPDATE service_orders 
                SET service_cost = (SELECT IFNULL(SUM(cost * quantity), 0) FROM repair_items WHERE service_order_id = NEW.service_order_id)
                WHERE id = NEW.service_order_id;
            END
        ");

        // 5. Trigger for service_orders cost consistency (AFTER DELETE repair_items)
        DB::unprepared("
            CREATE TRIGGER trg_repair_items_cost_delete AFTER DELETE ON repair_items
            FOR EACH ROW
            BEGIN
                UPDATE service_orders 
                SET service_cost = (SELECT IFNULL(SUM(cost * quantity), 0) FROM repair_items WHERE service_order_id = OLD.service_order_id)
                WHERE id = OLD.service_order_id;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_orders_total_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_orders_total_update");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_repair_items_cost_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_repair_items_cost_update");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_repair_items_cost_delete");
    }
};
