<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup customers data to a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting customer backup...');

        $customers = Customer::with(['devices', 'serviceOrders'])->get();
        $filename = 'backups/customers_full_backup_' . Carbon::now()->format('Y_m_d_H_i_s') . '.json';
        
        $data = $customers->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        Storage::disk('local')->put($filename, $data);

        $this->info("Backup created successfully: storage/app/{$filename}");

        // Cleanup: keep only last 10 backups
        $files = Storage::disk('local')->files('backups');
        $customerBackups = array_filter($files, fn($f) => str_contains($f, 'customers_full_backup_'));
        
        if (count($customerBackups) > 10) {
            $oldFiles = array_slice($customerBackups, 0, count($customerBackups) - 10);
            Storage::disk('local')->delete($oldFiles);
            $this->comment('Old backups cleaned up.');
        }
    }
}
