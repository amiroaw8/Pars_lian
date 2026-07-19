<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupExpiredCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-expired-carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired and inactive shopping carts from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting expired carts cleanup...');

        $expiredCount = \App\Models\Cart::where('expires_at', '<', now())
            ->orWhere('is_active', false)
            ->count();

        if ($expiredCount > 0) {
            // Delete associated items first (optional if using cascading deletes)
            $carts = \App\Models\Cart::where('expires_at', '<', now())
                ->orWhere('is_active', false)
                ->get();

            foreach ($carts as $cart) {
                $cart->items()->delete();
                $cart->delete();
            }

            $this->info("Successfully removed {$expiredCount} expired/inactive carts.");
        } else {
            $this->info('No expired carts found.');
        }

        return Command::SUCCESS;
    }
}
