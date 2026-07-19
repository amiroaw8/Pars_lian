<?php

namespace App\View\Components\Cells;

use App\Models\AccountingSale;
use App\Models\ServiceOrder;
use App\Models\Order;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class AccountingCell extends Component
{
    public array $stats;
    public $recentSales;

    public function __construct()
    {
        $this->stats = $this->getStats();
        $this->recentSales = $this->getRecentSales();
    }

    private function getStats(): array
    {
        return Cache::remember('cell_accounting_stats', 300, function () {
            return [
                'today_sales' => AccountingSale::whereDate('created_at', today())->sum('amount'),
                'monthly_income' => AccountingSale::whereMonth('created_at', now()->month)->sum('amount'),
                'total_repairs' => ServiceOrder::count(),
                'total_orders' => Order::count(),
            ];
        });
    }

    private function getRecentSales()
    {
        return AccountingSale::latest()->take(5)->get();
    }

    public function render()
    {
        return view('components.cells.accounting-cell');
    }
}
