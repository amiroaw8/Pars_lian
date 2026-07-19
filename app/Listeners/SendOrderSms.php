<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Events\PaymentStatusChanged;
use App\Jobs\SendSmsJob;
use App\Support\SmsNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderSms
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated|OrderStatusChanged|PaymentStatusChanged $event): void
    {
        $order = $event->order;
        $phone = $order->shipping_phone ?? $order->user?->phone;

        if (!$phone) {
            return;
        }

        $message = match (true) {
            $event instanceof OrderCreated => $order->getStatusSmsMessage(),
            $event instanceof OrderStatusChanged => $order->getStatusSmsMessage(),
            $event instanceof PaymentStatusChanged => $order->getPaymentStatusSmsMessage(),
            default => null,
        };

        if (! $message) {
            return;
        }

        $statusId = match (true) {
            $event instanceof PaymentStatusChanged => $order->payment_status->value,
            default => $order->status->value,
        };

        $enabled = match (true) {
            $event instanceof PaymentStatusChanged => SmsNotifications::isShopPaymentStatusEnabled($statusId),
            default => SmsNotifications::isShopOrderStatusEnabled($statusId),
        };

        if (! $enabled) {
            return;
        }

        SendSmsJob::dispatch($phone, $message);
    }
}
