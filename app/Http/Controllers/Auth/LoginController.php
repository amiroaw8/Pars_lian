<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\SessionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function __construct(
        private readonly SessionManager $sessions
    ) {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');

        if (!Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password'], 'is_active' => true], $remember)) {
            Log::warning('Failed login attempt', ['phone' => $credentials['phone'], 'ip' => $request->ip()]);

            return back()->withErrors([
                'phone' => 'شماره تلفن یا رمز عبور اشتباه است.',
            ]);
        }

        $guestCart = \App\Models\Cart::where('session_id', session()->getId())->first();

        $request->session()->regenerate();
        $request->session()->forget('two_factor_verified');

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            Auth::logout();

            return Redirect::back()->withErrors(['phone' => 'خطا در دریافت اطلاعات کاربر.']);
        }

        if ($guestCart) {
            $guestCart->mergeWithUserCart($user->id);
        }

        if ($this->sessions->exceedsLimit($user->id)) {
            return Redirect::route('auth.sessions.limit');
        }

        if ($user->needsTwoFactor()) {
            $user->generateTwoFactorCode();

            return Redirect::route('verify.index');
        }

        Log::info('User logged in successfully', ['user_id' => $user->id, 'ip' => $request->ip()]);

        return $this->redirectAfterLogin($user);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'با موفقیت از حساب خارج شدید.');
    }

    private function redirectAfterLogin(\App\Models\User $user)
    {
        if ($user->isSuperAdmin()) {
            return Redirect::route('super-admin.dashboard');
        }

        if ($user->isAdmin()) {
            return Redirect::route('admin.dashboard');
        }

        if ($user->isEmployee()) {
            return Redirect::route('automation.dashboard');
        }

        return Redirect::intended('/')->with('success', 'به فروشگاه خوش آمدید!');
    }
}
