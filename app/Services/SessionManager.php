<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SessionManager
{
    public function usesDatabaseDriver(): bool
    {
        return config('session.driver') === 'database';
    }

    public function maxActiveSessions(): int
    {
        return (int) config('session.max_active', 2);
    }

    public function pruneExpired(?int $userId = null): void
    {
        if (!$this->usesDatabaseDriver()) {
            return;
        }

        $cutoff = time() - ((int) config('session.lifetime', 120) * 60);

        $query = DB::table(config('session.table', 'sessions'))
            ->where('last_activity', '<', $cutoff);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $query->delete();
    }

    public function activeCountForUser(int $userId): int
    {
        if (!$this->usesDatabaseDriver()) {
            return 1;
        }

        $this->pruneExpired($userId);

        return (int) DB::table(config('session.table', 'sessions'))
            ->where('user_id', $userId)
            ->count();
    }

    public function exceedsLimit(int $userId): bool
    {
        return $this->activeCountForUser($userId) > $this->maxActiveSessions();
    }
}
