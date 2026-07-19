<?php

namespace App\View\Components\Cells;

use App\Models\Order;
use App\Models\User;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class SalesCell extends Component
{
    public array $stats;
    public $recentSales;

    public function __construct()
    {
        $user = Auth::user();
        $this->stats = $this->getStats($user);
        $this->recentSales = $this->getRecentSales($user);
    }

    private function getStats(User $user): array
    {
        return Cache::remember('cell_sales_stats_' . $user->id, 300, function () use ($user) {
            if ($user->isReceptionist()) {
                return [
                    'primary_value' => Order::whereDate('created_at', today())->count(),
                    'primary_label' => 'فروش‌های امروز',
                    'secondary_value' => Order::where('status', 'processing')->count(),
                    'secondary_label' => 'در حال پردازش',
                ];
            }

            return [
                'primary_value' => Order::where('status', 'pending')->count(),
                'primary_label' => 'سفارشات جدید',
                'secondary_value' => Order::where('status', 'processing')->count(),
                'secondary_label' => 'در حال پردازش',
            ];
        });
    }

    private function getRecentSales(User $user)
    {
        $query = Order::with(['user'])->latest();

        if (request()->filled('sales_search')) {
            $search = request('sales_search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->take(10)->get();
    }

    public function render()
    {
        return view('components.cells.sales-cell');
    }
}
