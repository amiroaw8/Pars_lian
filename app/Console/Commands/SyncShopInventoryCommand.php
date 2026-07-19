<?php

namespace App\Console\Commands;

use App\Services\ShopInventorySync;
use Illuminate\Console\Command;

class SyncShopInventoryCommand extends Command
{
    protected $signature = 'shop:sync-inventory {--inventory= : فقط یک شناسه انبار}';

    protected $description = 'همگام‌سازی موجودی محصولات فروشگاه با انبار (انبار = منبع حقیقت)';

    public function handle(): int
    {
        $inventoryId = $this->option('inventory');
        $id = $inventoryId !== null && $inventoryId !== '' ? (int) $inventoryId : null;

        $result = ShopInventorySync::reconcile($id);

        $this->info("محصولات متصل: {$result['linked']}");
        $this->info("به‌روزرسانی‌شده: {$result['synced']}");

        if (count($result['mismatches']) > 0) {
            $this->warn('ناسازگاری‌های اصلاح‌شده:');
            foreach ($result['mismatches'] as $row) {
                $this->line("  محصول #{$row['product_id']}: فروشگاه {$row['product_qty']} ← انبار {$row['inventory_qty']}");
            }
        } else {
            $this->info('ناسازگاری یافت نشد.');
        }

        return self::SUCCESS;
    }
}
