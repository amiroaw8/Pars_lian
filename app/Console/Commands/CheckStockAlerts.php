<?php

namespace App\Console\Commands;

use App\Support\SmsNotifications;
use Illuminate\Console\Command;

class CheckStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for low stock items and send SMS alerts to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockItems = \App\Models\Inventory::whereColumn('quantity', '<', 'min_quantity')->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('No low stock items found.');
            return;
        }

        if (! SmsNotifications::isInventoryAlertEnabled()) {
            $this->info('Inventory SMS alerts are disabled in settings.');
            return;
        }

        $count = $lowStockItems->count();
        $message = SmsNotifications::prepareInventoryBatchAlertMessage($lowStockItems);

        // Send to admins and warehouse managers
        $admins = \App\Models\User::role(['admin', 'super_admin', 'warehouse'])->get();
        $smsService = app(\App\Services\SMSService::class);

        $sentCount = 0;
        foreach ($admins as $admin) {
            if ($admin->phone) {
                try {
                    $smsService->sendSMS($admin->phone, $message);
                    $sentCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to send SMS to {$admin->name}: " . $e->getMessage());
                }
            }
        }

        $this->info("Stock alert check completed. Found {$count} items. SMS sent to {$sentCount} users.");
    }
}
