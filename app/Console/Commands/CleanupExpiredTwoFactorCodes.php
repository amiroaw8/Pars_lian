<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupExpiredTwoFactorCodes extends Command
{
    protected $signature = 'auth:cleanup-two-factor-codes';

    protected $description = 'Clear expired two-factor authentication codes from users table';

    public function handle(): int
    {
        $count = User::query()
            ->whereNotNull('two_factor_code')
            ->where(function ($query) {
                $query->whereNull('two_factor_expires_at')
                    ->orWhere('two_factor_expires_at', '<', now());
            })
            ->update([
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ]);

        $this->info("Cleared {$count} expired two-factor code(s).");

        return Command::SUCCESS;
    }
}
