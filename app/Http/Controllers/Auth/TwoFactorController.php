<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SessionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly SessionManager $sessions
    ) {
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->two_factor_expires_at && now()->gt($user->two_factor_expires_at)) {
            $user->generateTwoFactorCode();

            return view('auth.two_factor')->with('warning', 'کد قبلی منقضی شده است. کد جدید برای شما ارسال شد.');
        }

        return view('auth.two_factor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|integer',
        ]);

        $user = Auth::user();

        if ($user->two_factor_expires_at && now()->gt($user->two_factor_expires_at)) {
            return redirect()->back()->withErrors(['two_factor_code' => 'کد تایید منقضی شده است.']);
        }

        if ($request->input('two_factor_code') != $user->two_factor_code) {
            return redirect()->back()->withErrors(['two_factor_code' => 'کد تایید وارد شده صحیح نیست.']);
        }

        $user->resetTwoFactorCode();
        session()->put('two_factor_verified', true);

        if ($this->sessions->exceedsLimit($user->id)) {
            return Redirect::route('auth.sessions.limit');
        }

        if ($user->isSuperAdmin()) {
            return Redirect::route('super-admin.dashboard');
        }
        if ($user->isAdmin()) {
            return Redirect::route('admin.dashboard');
        }
        if ($user->isEmployee()) {
            return Redirect::route('automation.dashboard');
        }

        return redirect()->intended('/');
    }

    public function resend()
    {
        $user = Auth::user();

        if ($user->two_factor_expires_at && now()->gt($user->two_factor_expires_at)) {
            $user->generateTwoFactorCode();

            return redirect()->back()->with('success', 'کد قبلی منقضی شده بود. کد تایید جدید ارسال شد.');
        }

        if ($user->two_factor_code && $user->two_factor_expires_at && now()->lt($user->two_factor_expires_at)) {
            return redirect()->back()->with('warning', 'کد فعلی هنوز معتبر است. لطفاً همان کد را وارد کنید.');
        }

        $user->generateTwoFactorCode();

        return redirect()->back()->with('success', 'کد تایید جدید ارسال شد.');
    }
}
