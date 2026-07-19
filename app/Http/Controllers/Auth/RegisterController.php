<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
            if ($existingUser->hasRole('customer')) {
                return back()->withErrors([
                    'phone' => 'این شماره قبلاً ثبت شده است. لطفاً وارد حساب کاربری خود شوید.',
                ])->withInput();
            }

            return back()->withErrors([
                'phone' => 'این شماره تلفن قابل استفاده برای ثبت‌نام مشتری نیست.',
            ])->withInput();
        }

        $existingCustomer = Customer::where('phone', $request->phone)->first();
        if ($existingCustomer?->user_id) {
            return back()->withErrors([
                'phone' => 'این شماره قبلاً در سیستم ثبت شده است. لطفاً وارد حساب کاربری خود شوید.',
            ])->withInput();
        }

        $user = new User([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);
        $user->forceFill(['password' => Hash::make($request->password)]);
        $user->save();

        // Assign customer role by default
        $user->assignRole('customer');

        // Get guest cart before login session regeneration
        $guestSessionId = session()->getId();
        $guestCart = \App\Models\Cart::where('session_id', $guestSessionId)->first();

        // Login user before sync to ensure activity logs have user_id
        Auth::login($user);

        // Sync with Customers table (merge in-person records created at reception)
        if ($existingCustomer) {
            $existingCustomer->update([
                'name' => $user->name,
                'user_id' => $user->id,
            ]);
        } else {
            Customer::create([
                'phone' => $request->phone,
                'name' => $user->name,
                'user_id' => $user->id,
            ]);
        }

        // Merge guest cart with user cart if exists
        if ($guestCart) {
            $guestCart->mergeWithUserCart($user->id);
        }

        return redirect()->route('customer.dashboard')->with('new_registration', true);
    }
}
