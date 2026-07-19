<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Models\SMSLog;
use App\Support\SmsNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected readonly string $apiKey;
    protected readonly string $lineNumber;
    protected readonly string $baseUrl;

    public function __construct(
        ?string $apiKey = null,
        ?string $lineNumber = null,
        ?string $baseUrl = null
    ) {
        $this->apiKey = $apiKey ?? (string) config('services.smsir.api_key', '');
        $this->lineNumber = $lineNumber ?? (string) config('services.smsir.line_number', '');
        $this->baseUrl = $baseUrl ?? 'https://api.sms.ir/';
    }

    /**
     * Prepare status update SMS message for a service order
     */
    public function prepareServiceOrderMessage(ServiceOrder $order, string $statusId): ?string
    {
        $status = DB::table('service_order_statuses')->where('id', $statusId)->first();

        if (!$status || empty($status->sms_template)) {
            return null;
        }

        if (! (bool) ($status->sms_enabled ?? true)) {
            return null;
        }

        $template = $status->sms_template;

        return $this->applyOrderPlaceholders($order, $template);
    }

    public function prepareOrderRegisteredMessage(ServiceOrder $order): string
    {
        return $this->applyOrderPlaceholders($order, SmsNotifications::orderRegisteredTemplate());
    }

    public function prepareDebtNotificationMessage(ServiceOrder $order): string
    {
        $template = SmsNotifications::debtNotificationTemplate();

        return $this->applyOrderPlaceholders($order, $template, (float) ($order->debt_amount ?? 0));
    }

    private function applyOrderPlaceholders(ServiceOrder $order, string $template, ?float $costOverride = null): string
    {
        $order->loadMissing(['device', 'technician', 'customer']);

        $replacements = array_merge(SmsNotifications::businessPlaceholders(), [
            '{id}' => $order->id,
            '{customer_name}' => $order->receiver_name ?: ($order->customer?->name ?? 'مشتری'),
            '{device}' => $order->device
                ? trim(($order->device->type ?? '') . ' ' . ($order->device->model ?? ''))
                : 'دستگاه',
            '{cost}' => number_format($costOverride ?? (float) ($order->service_cost ?? 0)),
            '{technician_name}' => $order->technician ? $order->technician->name : 'کارشناس فنی',
        ]);

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Send status update SMS for a service order
     */
    public function sendServiceOrderUpdate(ServiceOrder $order, string $statusId): ?SMSLog
    {
        $message = $this->prepareServiceOrderMessage($order, $statusId);

        if (!$message) {
            return null;
        }

        return $this->sendSMS($order->receiver_phone, $message, $order->id);
    }

    public function sendSMS(string $phone, string $message, ?int $serviceOrderId = null): SMSLog
    {
        // اگر API Key تنظیم نشده، فقط در لاگ ذخیره شود
        if (empty($this->apiKey)) {
            return $this->logSMS($phone, $message, 'no_api_key', null, $serviceOrderId, 'CONFIG_ERROR', 'API Key is not set');
        }

        try {
            $headers = [
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $data = [
                'lineNumber' => (int) $this->lineNumber,
                'messageText' => $message,
                'mobiles' => [$phone],
            ];

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->post($this->baseUrl . 'v1/send/bulk', $data);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == 1) {
                $smsId = $result['data']['messageIds'][0] ?? null;

                return $this->logSMS($phone, $message, 'sent', (string) $smsId, $serviceOrderId, null, null, $result);
            }

            // استخراج جزئیات خطا بر اساس مستندات sms.ir
            $errorCode = (string) ($result['status'] ?? 'API_ERROR');
            $errorMessage = $result['message'] ?? 'Unknown API error';

            // مپ کردن کدهای خطای رایج برای درک بهتر کاربر
            $friendlyMessage = $this->getFriendlyErrorMessage($errorCode, $errorMessage);

            Log::error("SMS.ir API error ({$errorCode}): " . $errorMessage);
            
            return $this->logSMS(
                $phone, 
                $message, 
                'failed', 
                null, 
                $serviceOrderId, 
                $errorCode, 
                $friendlyMessage, 
                $result
            );

        } catch (\Exception $e) {
            Log::error('SMS sending exception: ' . $e->getMessage());

            return $this->logSMS(
                $phone, 
                $message, 
                'error', 
                null, 
                $serviceOrderId, 
                'EXCEPTION', 
                'خطای سیستمی در ارسال: ' . $e->getMessage()
            );
        }
    }

    /**
     * تبدیل کدهای خطا به پیام‌های فارسی قابل درک
     */
    protected function getFriendlyErrorMessage(string $code, string $default): string
    {
        $messages = [
            '10' => 'اعتبار پنل کافی نیست.',
            '11' => 'خط فرستنده مسدود یا غیرفعال است.',
            '12' => 'شماره گیرنده در لیست سیاه (Blacklist) است.',
            '13' => 'محتوای پیامک توسط فیلترینگ پنل رد شده است.',
            '14' => 'تعداد شماره‌های ارسالی بیش از حد مجاز است.',
            '101' => 'API Key نامعتبر است.',
            '401' => 'دسترسی غیرمجاز (بررسی IP یا API Key).',
        ];

        return $messages[$code] ?? "خطای مخابراتی: {$default}";
    }

    public function getStatus(string $messageId): array
    {
        if (empty($this->apiKey)) {
            return ['status' => 'api_key_not_set'];
        }

        try {
            $headers = [
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ];

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl . 'v1/send/' . $messageId);

            return $response->json() ?? ['status' => 'unknown'];

        } catch (\Exception $e) {
            Log::error('SMS status check failed: ' . $e->getMessage());

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * دریافت اعتبار باقیمانده پنل
     */
    public function getBalance(): ?float
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $headers = [
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ];

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl . 'v1/credit');

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == 1) {
                return (float) ($result['data'] ?? 0);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('SMS credit check failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function logSMS(
        string $phone, 
        string $message, 
        string $status, 
        ?string $messageId = null, 
        ?int $serviceOrderId = null,
        ?string $errorCode = null,
        ?string $errorMessage = null,
        ?array $providerResponse = null
    ): SMSLog {
        $log = new SMSLog([
            'phone' => $phone,
            'message' => $message,
            'sms_id' => $messageId,
            'service_order_id' => $serviceOrderId,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'provider_response' => $providerResponse,
            'api_key_set' => !empty($this->apiKey),
        ]);
        $log->forceFill(['status' => $status]);
        $log->save();
        return $log;
    }
}
