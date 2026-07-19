<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        $reset = PasswordReset::where('reset_token', $token)
            ->where('verified_at', '!=', null)
            ->first();

        if (! $reset) {
            abort(404, 'لینک بازیابی نامعتبر یا منقضی است.');
        }

        $user = User::where('phone', $reset->phone)->first();
        if (! $user) {
            abort(404, 'کاربر یافت نشد.');
        }

        return view('password_reset')->with([
            'token' => $token,
            'phone' => $reset->phone,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'phone' => 'required|digits:11',
            'password' => 'required|confirmed|min:8',
        ]);

        $reset = PasswordReset::where('reset_token', $request->token)
            ->where('phone', $request->phone)
            ->where('verified_at', '!=', null)
            ->first();

        if (! $reset) {
            return back()->withErrors(['token' => 'لینک بازیابی نامعتبر است.']);
        }

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return back()->withErrors(['phone' => 'کاربر یافت نشد.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $reset->delete();

        return redirect()->route('login')->with('success', 'رمز عبور با موفقیت تغییر یافت. لطفاً وارد شوید.');
    }
}
