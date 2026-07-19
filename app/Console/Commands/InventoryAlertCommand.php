<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendSmsJob;
use App\Models\Inventory;
use App\Models\User;
use App\Support\SmsNotifications;
use Illuminate\Support\Facades\Log;

class InventoryAlertCommand extends Command
{
    protected $signature = 'inventory:check-alerts';
    protected $description = 'Check inventory levels and send SMS alerts for low stock items';

    public function handle()
    {
        $lowStockItems = Inventory::whereColumn('quantity', '<=', 'min_quantity')->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('No low stock items found.');
            return;
        }

        if (! SmsNotifications::isInventoryAlertEnabled()) {
            $this->info('Inventory SMS alerts are disabled in settings.');
            return;
        }

        $message = SmsNotifications::prepareInventoryBatchAlertMessage($lowStockItems);

        // Find admins and warehouse managers
        $recipients = User::role(['super_admin', 'admin', 'warehouse'])
            ->whereNotNull('phone')
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('No recipients found for inventory alerts.');
            return;
        }

        foreach ($recipients as $user) {
            try {
                SendSmsJob::dispatch($user->phone, $message);
                $this->info("Alert queued for {$user->name} ({$user->phone})");
            } catch (\Exception $e) {
                Log::error("Failed to queue inventory alert SMS for {$user->name}: " . $e->getMessage());
                $this->error("Failed to queue SMS for {$user->name}");
            }
        }
        
        $this->info('Inventory check completed.');
    }
}
