<?php

namespace App\Http\Controllers;

use App\Models\SMSLog;
use App\Services\SMSService;
use App\Http\Requests\SendSMSRequest;
use App\Jobs\SendSmsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SMSController extends Controller
{
    public function __construct(
        private readonly SMSService $smsService
    ) {}

    public function sendSMS(SendSMSRequest $request)
    {
        try {
            SendSmsJob::dispatch(
                $request->phone,
                $request->message
            );

            return response()->json([
                'success' => true,
                'message' => 'پیامک در صف ارسال قرار گرفت',
            ]);

        } catch (\Exception $e) {
            Log::error('SMS queuing failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'خطا در ثبت پیامک: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getLogs(Request $request)
    {
        $logs = SMSLog::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function getStatus($smsId)
    {
        try {
            $status = $this->smsService->getStatus($smsId);

            // Check if the API response indicates success (status == 1)
            $isSuccess = isset($status['status']) && $status['status'] == 1;

            if (!$isSuccess) {
                return response()->json([
                    'success' => false,
                    'data' => $status,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت وضعیت SMS',
            ], 500);
        }
    }
}
