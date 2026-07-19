<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function all(): Collection
    {
        return Order::with(['user', 'items'])->latest()->get();
    }

    public function find(int $id): ?Order
    {
        return Order::with(['user', 'items.product'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['user', 'items.product'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['user', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Order
    {
        $order = new Order();
        $order->forceFill($data);
        $order->save();
        return $order;
    }

    public function update(int $id, array $data): bool
    {
        $order = Order::find($id);
        if (!$order) {
            return false;
        }
        $order->forceFill($data);
        return $order->save();
    }

    public function delete(int $id): bool
    {
        $order = Order::find($id);
        if (!$order) {
            return false;
        }
        return $order->delete();
    }

    public function getRecentOrders(int $limit = 5): Collection
    {
        return Order::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getStatusCounts(): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
