<?php

namespace App\Services;

use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Models\Attachment;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\CustomerInteraction;
use App\Models\Device;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AccountDeletionService
{
    public function __construct(
        private readonly SiteFileDeleter $fileDeleter
    ) {}

    public function softDeleteCustomer(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $this->deleteCustomerGraph($customer, force: false);
        });
    }

    public function forceDeleteCustomer(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $this->deleteCustomerGraph($customer, force: true);
        });
    }

    public function softDeleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteUserGraph($user, force: false);
        });
    }

    public function forceDeleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteUserGraph($user, force: true);
        });
    }

    private function deleteCustomerGraph(Customer $customer, bool $force): void
    {
        $customerId = $customer->id;
        $userId = $customer->user_id;

        ServiceOrder::withTrashed()
            ->where('customer_id', $customerId)
            ->orderBy('id')
            ->get()
            ->each(fn (ServiceOrder $order) => $this->deleteServiceOrder($order, $force));

        $this->shopOrdersQueryForCustomer($customer)
            ->withTrashed()
            ->orderBy('id')
            ->get()
            ->each(fn (Order $order) => $this->deleteShopOrder($order, $force));

        AccountingSale::query()->where('customer_id', $customerId)->delete();

        Device::withTrashed()
            ->where('customer_id', $customerId)
            ->get()
            ->each(fn (Device $device) => $force ? $device->forceDelete() : $device->delete());

        CustomerInteraction::query()->where('customer_id', $customerId)->delete();

        if ($userId) {
            $user = User::withTrashed()->find($userId);
            if ($user && $this->shouldDeleteLinkedUser($user)) {
                $this->deleteUserGraph($user, $force, deleteCustomer: false);
            }
        }

        $customerRecord = Customer::withTrashed()->find($customerId);
        if ($customerRecord) {
            $force ? $customerRecord->forceDelete() : $customerRecord->delete();
        }
    }

    private function deleteUserGraph(User $user, bool $force, bool $deleteCustomer = true): void
    {
        if ($deleteCustomer) {
            $customer = Customer::withTrashed()->where('user_id', $user->id)->first();
            if ($customer) {
                $this->deleteCustomerGraph($customer, $force);

                return;
            }
        }

        ServiceOrder::query()
            ->where('technician_id', $user->id)
            ->update(['technician_id' => null]);

        Order::withTrashed()
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get()
            ->each(fn (Order $order) => $this->deleteShopOrder($order, $force));

        Cart::query()->where('user_id', $user->id)->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->tokens()->delete();

        $userRecord = User::withTrashed()->find($user->id);
        if ($userRecord) {
            $force ? $userRecord->forceDelete() : $userRecord->delete();
        }
    }

    private function deleteServiceOrder(ServiceOrder $order, bool $force): void
    {
        $orderId = $order->id;

        Attachment::withTrashed()
            ->where('attachable_type', ServiceOrder::class)
            ->where('attachable_id', $orderId)
            ->get()
            ->each(function (Attachment $attachment) use ($force) {
                if ($force) {
                    $this->fileDeleter->purgeAttachment($attachment);
                } elseif (! $attachment->trashed()) {
                    $attachment->delete();
                }
            });

        $order->repairItems()->withTrashed()->get()->each(
            fn ($item) => $force ? $item->forceDelete() : $item->delete()
        );

        AccountingService::query()->where('service_order_id', $orderId)->delete();
        $order->smsLogs()->delete();
        $order->orderLogs()->delete();

        Order::withTrashed()
            ->where('service_order_id', $orderId)
            ->orderBy('id')
            ->get()
            ->each(fn (Order $shopOrder) => $this->deleteShopOrder($shopOrder, $force));

        $fresh = ServiceOrder::withTrashed()->find($orderId);
        if ($fresh) {
            $force ? $fresh->forceDelete() : $fresh->delete();
        }
    }

    private function deleteShopOrder(Order $order, bool $force): void
    {
        $orderId = $order->id;

        AccountingSale::query()->where('order_id', $orderId)->delete();
        PaymentTransaction::query()->where('order_id', $orderId)->delete();

        $order->items()->withTrashed()->get()->each(
            fn ($item) => $force ? $item->forceDelete() : $item->delete()
        );

        $fresh = Order::withTrashed()->find($orderId);
        if ($fresh) {
            $force ? $fresh->forceDelete() : $fresh->delete();
        }
    }

    private function shopOrdersQueryForCustomer(Customer $customer): Builder
    {
        return Order::query()->where(function ($query) use ($customer) {
            if ($customer->user_id) {
                $query->where('user_id', $customer->user_id);
            }

            PhoneNumber::scopeWherePhoneMatches($query, 'shipping_phone', $customer->phone);
        });
    }

    private function shouldDeleteLinkedUser(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return false;
        }

        return ! $user->isEmployee();
    }
}
