<?php

namespace App\Jobs;

use App\Services\SMSService;
use App\Models\SMSLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $phone,
        protected string $message,
        protected ?int $serviceOrderId = null
    ) {}

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getServiceOrderId(): ?int
    {
        return $this->serviceOrderId;
    }

    public function handle(SMSService $smsService): void
    {
        // Prevent accidental duplicate sends: skip if an identical log exists in last 2 minutes
        $exists = SMSLog::where('phone', $this->phone)
            ->where('message', $this->message)
            ->where('service_order_id', $this->serviceOrderId)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($exists) {
            return;
        }

        $smsService->sendSMS($this->phone, $this->message, $this->serviceOrderId);
    }
}
