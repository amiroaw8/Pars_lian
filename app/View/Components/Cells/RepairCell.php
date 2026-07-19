<?php

namespace App\View\Components\Cells;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class RepairCell extends Component
{
    public array $stats;
    public $recentRepairs;

    public function __construct()
    {
        $user = Auth::user();
        $this->stats = $this->getStats($user);
        $this->recentRepairs = $this->getRecentRepairs($user);
    }

    private function getStats(User $user): array
    {
        return Cache::remember('cell_repair_stats_' . $user->id, 300, function () use ($user) {
            $repairStats = [
                'pending' => ServiceOrder::whereIn('status', ['registered', 'repairing'])->count(),
                'ready' => ServiceOrder::where('status', 'ready')->count(),
            ];

            if ($user->isReceptionist()) {
                return [
                    'primary_value' => ServiceOrder::whereDate('created_at', today())->count(),
                    'primary_label' => 'ثبت نام‌های امروز',
                    'secondary_value' => ServiceOrder::where('status', 'ready')->count(),
                    'secondary_label' => 'آماده تحویل (کل)',
                ];
            }

            return [
                'primary_value' => $repairStats['pending'],
                'primary_label' => 'در انتظار تعمیر',
                'secondary_value' => $repairStats['ready'],
                'secondary_label' => 'آماده تحویل',
            ];
        });
    }

    private function getRecentRepairs(User $user)
    {
        $query = ServiceOrder::with(['customer', 'device'])->latest();

        if (request()->filled('repair_search')) {
            $search = request('repair_search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('device', fn($dq) => $dq->where('model', 'like', "%{$search}%"));
            });
        }

        if ($user->isTechnician()) {
            $query->where('technician_id', $user->id);
        }

        return $query->take(10)->get();
    }

    public function render()
    {
        return view('components.cells.repair-cell');
    }
}
