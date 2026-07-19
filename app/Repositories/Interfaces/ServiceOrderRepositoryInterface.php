<?php

namespace App\Repositories\Interfaces;

use App\Models\ServiceOrder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceOrderRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?ServiceOrder;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): ServiceOrder;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getRecentOrders(int $limit = 5): Collection;
    public function getStatusCounts(): array;
}
