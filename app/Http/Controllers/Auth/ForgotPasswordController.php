<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\SMSService;
use App\Support\SmsNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    protected SMSService $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->middleware('guest');
        $this->smsService = $smsService;
    }

    public function showLinkRequestForm()
    {
        return view('password_phone');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['phone' => 'required|digits:11|starts_with:09']);

        // Rate limit: 3 attempts per 60 minutes
        $key = 'reset_code_' . $request->phone;
        if (\Illuminate\Support\Facades\Cache::has($key)) {
            return back()->withInput()->withErrors(['phone' => 'درخواست قبلی هنوز معتبر است. بعد از ۱۵ دقیقه دوباره تلاش کنید.']);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return back()->withInput()->withErrors(['phone' => 'کاربری با این شماره تلفن یافت نشد.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        PasswordReset::updateOrCreate(
            ['phone' => $request->phone],
            ['code' => $code, 'expires_at' => now()->addMinutes(15)]
        );

        // Record this attempt to prevent spam
        \Illuminate\Support\Facades\Cache::put($key, true, 900); // 15 minutes

        try {
            if (SmsNotifications::isPasswordResetEnabled()) {
                $this->smsService->sendSMS(
                    $request->phone,
                    SmsNotifications::preparePasswordResetMessage($code)
                );
            }
            return redirect()->route('password.verify-code')->with(['phone' => $request->phone, 'success' => 'کد تایید به شماره تلفن شما ارسال شد.']);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['phone' => 'خطا در ارسال پیامک. لطفاً دوباره تلاش کنید.']);
        }
    }

    public function showVerifyCodeForm()
    {
        if (!session('phone')) {
            return redirect()->route('password.request');
        }
        return view('password_verify_code', ['phone' => session('phone')]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:11|starts_with:09',
            'code' => 'required|size:6'
        ]);

        $reset = PasswordReset::where('phone', $request->phone)->first();
        if (!$reset || $reset->expires_at < now()) {
            return back()->withErrors(['code' => 'کد منقضی یا نامعتبر است.']);
        }

        if ($reset->code !== $request->code) {
            return back()->withErrors(['code' => 'کد وارد شده اشتباه است.']);
        }

        $token = Str::random(60);
        $reset->update(['verified_at' => now(), 'reset_token' => $token]);

        return redirect()->route('password.reset', ['token' => $token])->with(['phone' => $request->phone]);
    }
}
