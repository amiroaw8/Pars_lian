<?php

namespace App\Listeners;

use App\Events\ServiceOrderCreated;
use App\Events\ServiceOrderStatusChanged;
use App\Jobs\SendSmsJob;
use App\Services\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendServiceOrderSms
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SMSService $smsService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ServiceOrderCreated|ServiceOrderStatusChanged $event): void
    {
        $order = $event->order;

        // Get status ID safely
        $statusId = $order->status instanceof \App\Enums\ServiceOrderStatus
            ? $order->status->value
            : $order->status;

        // Never resend "order registered" SMS on status changes (only on ServiceOrderCreated)
        if ($event instanceof ServiceOrderStatusChanged && $statusId === \App\Enums\ServiceOrderStatus::REGISTERED->value) {
            return;
        }

        if ($event instanceof ServiceOrderCreated && ! \App\Support\SmsNotifications::isOrderRegisteredEnabled()) {
            return;
        }

        // Skip SMS on technician assignment (registration SMS already sent at create)
        if ($event instanceof ServiceOrderStatusChanged && $statusId === \App\Enums\ServiceOrderStatus::TECHNICIAN_ASSIGNED->value) {
            return;
        }

        if ($event instanceof ServiceOrderStatusChanged && ! \App\Support\SmsNotifications::isStatusEnabled((string) $statusId)) {
            return;
        }

        if (empty($order->receiver_phone)) {
            $order->loadMissing('customer');
            if ($order->customer?->phone) {
                $order->receiver_phone = $order->customer->phone;
                $order->saveQuietly();
            } else {
                return;
            }
        }

        // ثبت بدهی → وضعیت «آماده تحویل» ولی پیامک «پرداخت تایید شد» نباید ارسال شود
        if ($statusId === \App\Enums\ServiceOrderStatus::READY->value && (float) ($order->debt_amount ?? 0) > 0) {
            if (! \App\Support\SmsNotifications::isDebtNotificationEnabled()) {
                return;
            }
            $debtMessage = $this->smsService->prepareDebtNotificationMessage($order);
            SendSmsJob::dispatchSync($order->receiver_phone, $debtMessage, $order->id);

            return;
        }

        if ($event instanceof ServiceOrderCreated) {
            $message = $this->smsService->prepareOrderRegisteredMessage($order);
            SendSmsJob::dispatchSync($order->receiver_phone, $message, $order->id);

            return;
        }

        $message = $this->smsService->prepareServiceOrderMessage($order, (string) $statusId);
        
        if ($message) {
            SendSmsJob::dispatchSync(
                $order->receiver_phone,
                $message,
                $order->id
            );
        }
    }
}
