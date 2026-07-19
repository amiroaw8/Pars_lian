<?php

namespace App\View\Components\Cells;

use App\Models\Inventory;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class WarehouseCell extends Component
{
    public int $lowStockCount;
    public $lowStockItems;

    public function __construct()
    {
        $this->lowStockCount = $this->getLowStockCount();
        $this->lowStockItems = $this->getLowStockItems();
    }

    private function getLowStockCount(): int
    {
        return Cache::remember('cell_warehouse_low_stock_count', 600, function () {
            return Inventory::whereRaw('quantity <= min_quantity')->count();
        });
    }

    private function getLowStockItems()
    {
        return Inventory::whereRaw('quantity <= min_quantity')
            ->orderBy('quantity', 'asc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('components.cells.warehouse-cell');
    }
}
