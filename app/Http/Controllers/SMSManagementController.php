<?php

namespace App\Http\Controllers;

use App\Models\SMSLog;
use App\Services\SMSService;
use App\Http\Requests\SendTestSMSRequest;
use App\Jobs\SendSmsJob;

class SMSManagementController extends Controller
{
    public function __construct(
        private readonly SMSService $smsService
    ) {}

    public function dashboard()
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isReceptionist()) {
            abort(403);
        }

        // آمار کلی
        $totalSMS = SMSLog::count();
        $sentSMS = SMSLog::where('status', 'sent')->count();
        $failedSMS = SMSLog::where('status', 'failed')->count();
        $pendingSMS = SMSLog::where('status', 'pending')->count();

        // موجودی حساب
        $balance = $this->smsService->getBalance();

        // لاگ‌های recent
        $recentLogs = SMSLog::with('serviceOrder')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('sms.dashboard', compact(
            'totalSMS',
            'sentSMS',
            'failedSMS',
            'pendingSMS',
            'balance',
            'recentLogs'
        ));
    }

    public function logs()
    {
        $logs = SMSLog::with('serviceOrder')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('sms.logs', compact('logs'));
    }

    public function sendTestSMS(SendTestSMSRequest $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isSuperAdmin()) {
            abort(403, 'فقط مدیران سیستم اجازه ارسال پیامک تستی را دارند.');
        }

        try {
            $log = $this->smsService->sendSMS(
                $request->phone,
                $request->message
            );

            if ($log->status === 'sent') {
                return back()->with('success', 'پیامک تستی با موفقیت ارسال شد. شناسه: ' . $log->sms_id);
            } else {
                return back()->with('error', 'ارسال پیامک با خطا مواجه شد: ' . ($log->error_message ?? 'خطای نامشخص'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'خطای سیستمی در ارسال پیامک: ' . $e->getMessage());
        }
    }

    public function getBalance()
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $balance = $this->smsService->getBalance();

        return response()->json([
            'balance' => $balance,
            'message' => $balance !== null ? "موجودی: {$balance} پیامک" : 'نامشخص',
        ]);
    }

    public function getStats()
    {
        $today = now()->startOfDay();

        $stats = [
            'today' => SMSLog::where('created_at', '>=', $today)->count(),
            'this_week' => SMSLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => SMSLog::where('created_at', '>=', now()->startOfMonth())->count(),
            'success_rate' => SMSLog::count() > 0
                ? round((SMSLog::where('status', 'sent')->count() / SMSLog::count()) * 100, 2)
                : 0,
        ];

        return response()->json($stats);
    }
}
