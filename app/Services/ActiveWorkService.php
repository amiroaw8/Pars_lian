<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Support\RoleLabels;
use Illuminate\Support\Collection;

class ActiveWorkService
{
    /** @var list<string> */
    private const ROLE_ORDER = [
        'receptionist',
        'technician',
        'warehouse',
        'accountant',
        'admin',
        'super_admin',
    ];

    /**
     * @return list<array{key: string, label: string, icon: string, accent: string, cartable_url: string|null, items: list<array<string, mixed>>, count: int}>
     */
    public function sectionsFor(User $user): array
    {
        $sections = [];

        foreach ($this->roleKeysFor($user) as $roleKey) {
            $items = $this->itemsForRole($user, $roleKey);

            $sections[] = [
                'key' => $roleKey,
                'label' => RoleLabels::label($roleKey),
                'icon' => RoleLabels::icon($roleKey),
                'accent' => RoleLabels::accent($roleKey),
                'cartable_url' => $this->cartableUrlFor($roleKey),
                'items' => $items,
                'count' => count($items),
            ];
        }

        return $sections;
    }

    /** @return list<string> */
    private function roleKeysFor(User $user): array
    {
        return collect(self::ROLE_ORDER)
            ->filter(fn (string $role) => $user->hasRole($role))
            ->values()
            ->all();
    }

  private function cartableUrlFor(string $roleKey): ?string
    {
        return match ($roleKey) {
            'receptionist' => route('automation.dashboard.reception'),
            'technician' => route('automation.dashboard.repair'),
            'warehouse' => route('automation.inventory.index'),
            'accountant' => route('automation.dashboard.accounting'),
            'admin', 'super_admin' => route('automation.dashboard'),
            default => null,
        };
    }

    /** @return list<array<string, mixed>> */
    private function itemsForRole(User $user, string $roleKey): array
    {
        $items = match ($roleKey) {
            'receptionist' => $this->receptionistItems(),
            'technician' => $this->technicianItems($user),
            'warehouse' => $this->warehouseItems(),
            'accountant' => $this->accountantItems(),
            'admin', 'super_admin' => $this->adminItems(),
            default => collect(),
        };

        return $items
            ->sortByDesc('updated_at')
            ->values()
            ->take(20)
            ->all();
    }

    /** @return Collection<int, array<string, mixed>> */
    private function receptionistItems(): Collection
    {
        $serviceOrders = ServiceOrder::query()
            ->with(['customer', 'device'])
            ->whereIn('status', [
                ServiceOrderStatus::REGISTERED,
                ServiceOrderStatus::READY,
                ServiceOrderStatus::REJECTED,
            ])
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get()
            ->map(fn (ServiceOrder $order) => $this->mapServiceOrder($order));

        $shopOrders = Order::query()
            ->with('user')
            ->whereIn('status', [OrderStatus::PENDING, OrderStatus::PROCESSING])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (Order $order) => $this->mapShopOrder($order));

        return $serviceOrders->concat($shopOrders);
    }

    /** @return Collection<int, array<string, mixed>> */
    private function technicianItems(User $user): Collection
    {
        return ServiceOrder::query()
            ->with(['customer', 'device'])
            ->where('technician_id', $user->id)
            ->whereIn('status', [
                ServiceOrderStatus::TECHNICIAN_ASSIGNED,
                ServiceOrderStatus::REPAIRING,
                ServiceOrderStatus::PENDING_PARTS,
                ServiceOrderStatus::SENT_TO_WORKSHOP,
                ServiceOrderStatus::REGISTERED,
            ])
            ->orderByDesc('updated_at')
            ->limit(25)
            ->get()
            ->map(fn (ServiceOrder $order) => $this->mapServiceOrder($order));
    }

    /** @return Collection<int, array<string, mixed>> */
    private function warehouseItems(): Collection
    {
        return Inventory::query()
            ->where('min_quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->orderByDesc('updated_at')
            ->limit(25)
            ->get()
            ->map(fn (Inventory $item) => [
                'id' => 'inventory-'.$item->id,
                'type' => 'inventory',
                'title' => $item->name,
                'subtitle' => 'موجودی: '.$item->quantity.' / حداقل: '.$item->min_quantity,
                'status_label' => 'کمبود موجودی',
                'status_color' => 'orange',
                'url' => route('automation.inventory.edit', $item),
                'updated_at' => $item->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                'updated_at_human' => $item->updated_at?->diffForHumans() ?? '',
            ]);
    }

    /** @return Collection<int, array<string, mixed>> */
    private function accountantItems(): Collection
    {
        $serviceOrders = ServiceOrder::query()
            ->with(['customer', 'device'])
            ->where('status', ServiceOrderStatus::ACCOUNTING)
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get()
            ->map(fn (ServiceOrder $order) => $this->mapServiceOrder($order));

        $shopOrders = Order::query()
            ->with('user')
            ->whereIn('status', [OrderStatus::PENDING, OrderStatus::PROCESSING])
            ->where('payment_status', PaymentStatus::PENDING)
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get()
            ->map(fn (Order $order) => $this->mapShopOrder($order));

        return $serviceOrders->concat($shopOrders);
    }

    /** @return Collection<int, array<string, mixed>> */
    private function adminItems(): Collection
    {
        $pendingAssignment = ServiceOrder::query()
            ->with(['customer', 'device'])
            ->where('status', ServiceOrderStatus::REGISTERED)
            ->whereNull('technician_id')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (ServiceOrder $order) => $this->mapServiceOrder($order, 'بدون تکنسین'));

        $accounting = ServiceOrder::query()
            ->with(['customer', 'device'])
            ->where('status', ServiceOrderStatus::ACCOUNTING)
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(fn (ServiceOrder $order) => $this->mapServiceOrder($order));

        $shopPending = Order::query()
            ->with('user')
            ->where('status', OrderStatus::PENDING)
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(fn (Order $order) => $this->mapShopOrder($order));

        return $pendingAssignment->concat($accounting)->concat($shopPending);
    }

    /** @return array<string, mixed> */
    private function mapServiceOrder(ServiceOrder $order, ?string $statusOverride = null): array
    {
        $status = $order->status instanceof ServiceOrderStatus
            ? $order->status
            : ServiceOrderStatus::tryFrom((string) $order->status);

        $customerName = $order->customer?->name ?? 'مشتری نامشخص';
        $deviceLabel = $order->device?->model ?? $order->device?->type ?? 'دستگاه';

        return [
            'id' => 'service-order-'.$order->id,
            'type' => 'service_order',
            'title' => $customerName.' — '.$deviceLabel,
            'subtitle' => 'پذیرش #'.($order->tracking_code ?? $order->id),
            'status_label' => $statusOverride ?? ($status?->label() ?? 'نامشخص'),
            'status_color' => $status?->color() ?? 'slate',
            'url' => route('automation.service-orders.show', $order),
            'updated_at' => $order->updated_at?->toIso8601String() ?? now()->toIso8601String(),
            'updated_at_human' => $order->updated_at?->diffForHumans() ?? '',
        ];
    }

    /** @return array<string, mixed> */
    private function mapShopOrder(Order $order): array
    {
        return [
            'id' => 'shop-order-'.$order->id,
            'type' => 'shop_order',
            'title' => $order->order_number ?? ('سفارش #'.$order->id),
            'subtitle' => $order->user?->name ?? 'مشتری فروشگاه',
            'status_label' => $order->status?->label() ?? 'سفارش آنلاین',
            'status_color' => 'blue',
            'url' => route('automation.orders.show', $order),
            'updated_at' => $order->updated_at?->toIso8601String() ?? now()->toIso8601String(),
            'updated_at_human' => $order->updated_at?->diffForHumans() ?? '',
        ];
    }
}
