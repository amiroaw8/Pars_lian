<?php

namespace App\Support;

use App\Models\AccountingExpense;
use App\Models\Attachment;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\RepairItem;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\SiteFileDeleter;
use App\Support\HashRef;
use Closure;
use Illuminate\Database\Eloquent\Model;

class RecycleBinRegistry
{
    /**
     * @return array<string, array{
     *     model: class-string<Model>,
     *     label: string,
     *     title: Closure(Model): string,
     *     before_force_delete?: Closure(Model): void
     * }>
     */
    public static function types(): array
    {
        return [
            'attachments' => [
                'model' => Attachment::class,
                'label' => 'پیوست‌ها',
                'title' => fn (Model $item) => (string) ($item->name ?? 'پیوست #'.$item->id),
                'before_force_delete' => fn (Model $item) => app(SiteFileDeleter::class)->purgeAttachment($item),
            ],
            'products' => [
                'model' => Product::class,
                'label' => 'محصولات',
                'title' => fn (Model $item) => (string) ($item->name ?? 'محصول #'.$item->id),
            ],
            'categories' => [
                'model' => ProductCategory::class,
                'label' => 'دسته‌بندی‌ها',
                'title' => fn (Model $item) => (string) ($item->name ?? 'دسته #'.$item->id),
            ],
            'brands' => [
                'model' => Brand::class,
                'label' => 'برندها',
                'title' => fn (Model $item) => (string) ($item->name ?? 'برند #'.$item->id),
            ],
            'users' => [
                'model' => User::class,
                'label' => 'کاربران',
                'title' => fn (Model $item) => (string) ($item->name ?? 'کاربر #'.$item->id),
            ],
            'customers' => [
                'model' => Customer::class,
                'label' => 'مشتریان',
                'title' => fn (Model $item) => (string) ($item->name ?? $item->receiver_name ?? 'مشتری #'.$item->id),
            ],
            'devices' => [
                'model' => Device::class,
                'label' => 'دستگاه‌ها',
                'title' => fn (Model $item) => (string) ($item->name ?? 'دستگاه #'.$item->id),
            ],
            'device_types' => [
                'model' => DeviceType::class,
                'label' => 'انواع دستگاه',
                'title' => fn (Model $item) => (string) ($item->name ?? 'نوع دستگاه #'.$item->id),
            ],
            'service_orders' => [
                'model' => ServiceOrder::class,
                'label' => 'سفارشات تعمیر',
                'title' => fn (Model $item) => 'سفارش تعمیر '.HashRef::plain($item->id),
            ],
            'orders' => [
                'model' => Order::class,
                'label' => 'سفارشات فروشگاه',
                'title' => fn (Model $item) => $item->order_number
                    ? HashRef::plain($item->order_number)
                    : 'سفارش '.HashRef::plain($item->id),
            ],
            'order_items' => [
                'model' => OrderItem::class,
                'label' => 'اقلام سفارش فروشگاه',
                'title' => fn (Model $item) => (string) ($item->product_name ?? 'قلم #'.$item->id),
            ],
            'inventories' => [
                'model' => Inventory::class,
                'label' => 'کالاهای انبار',
                'title' => fn (Model $item) => (string) ($item->name ?? 'کالا #'.$item->id),
            ],
            'inventory_transactions' => [
                'model' => InventoryTransaction::class,
                'label' => 'تراکنش‌های انبار',
                'title' => fn (Model $item) => (string) ($item->notes ?: 'تراکنش #'.$item->id),
            ],
            'repair_items' => [
                'model' => RepairItem::class,
                'label' => 'اقلام تعمیر',
                'title' => fn (Model $item) => (string) ($item->name ?? 'قلم تعمیر #'.$item->id),
            ],
            'accounting_expenses' => [
                'model' => AccountingExpense::class,
                'label' => 'هزینه‌های حسابداری',
                'title' => fn (Model $item) => (string) ($item->title ?? 'هزینه #'.$item->id),
            ],
        ];
    }

    /**
     * @return class-string<Model>|null
     */
    public static function modelClass(string $type): ?string
    {
        return self::types()[$type]['model'] ?? null;
    }

    public static function label(string $type): string
    {
        return self::types()[$type]['label'] ?? $type;
    }

    public static function title(string $type, Model $item): string
    {
        $config = self::types()[$type] ?? null;

        if (! $config) {
            return (string) ($item->name ?? $item->title ?? 'ID: '.$item->id);
        }

        return $config['title']($item);
    }

  /**
     * @return array<string, \Illuminate\Support\Collection<int, Model>>
     */
    public static function deletedItems(): array
    {
        $items = [];

        foreach (self::types() as $type => $config) {
            $items[$type] = $config['model']::onlyTrashed()->latest('deleted_at')->get();
        }

        return $items;
    }

    public static function runBeforeForceDelete(string $type, Model $item): void
    {
        $callback = self::types()[$type]['before_force_delete'] ?? null;

        if ($callback) {
            $callback($item);
        }
    }
}
