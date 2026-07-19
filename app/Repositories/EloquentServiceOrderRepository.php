<?php

namespace App\Repositories;

use App\Models\ServiceOrder;
use App\Repositories\Interfaces\ServiceOrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    public function all(): Collection
    {
        return ServiceOrder::with(['customer', 'device', 'technician'])->latest()->get();
    }

    public function find(int $id): ?ServiceOrder
    {
        return ServiceOrder::with(['customer', 'device', 'technician', 'repairItems', 'logs', 'attachments'])->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ServiceOrder::with(['customer', 'device', 'technician'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): ServiceOrder
    {
        $order = new ServiceOrder();
        $order->forceFill($data);
        $order->save();
        return $order;
    }

    public function update(int $id, array $data): bool
    {
        $order = ServiceOrder::find($id);
        if (!$order) {
            return false;
        }
        $order->forceFill($data);
        return $order->save();
    }

    public function delete(int $id): bool
    {
        $order = ServiceOrder::find($id);
        if (!$order) {
            return false;
        }
        return $order->delete();
    }

    public function getRecentOrders(int $limit = 5): Collection
    {
        return ServiceOrder::with(['customer', 'device'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getStatusCounts(): array
    {
        return ServiceOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
