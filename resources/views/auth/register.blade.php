@extends('layouts.auth')

@section('title', 'ثبت‌نام مشتری - پارس لیان')

@section('content')
<div class="text-center mb-8 md:mb-10">
    <div class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-2xl md:rounded-3xl bg-blue-600/10 text-blue-500 mb-4 md:mb-6 border border-blue-500/20">
        <i class="ti ti-user-plus text-3xl md:text-4xl"></i>
    </div>
    <h1 class="text-2xl md:text-3xl font-black text-white mb-2 md:mb-3">ایجاد حساب مشتری</h1>
    <p class="text-slate-400 font-medium text-base md:text-lg">به خانواده پارس لیان بپیوندید</p>
</div>

<div class="animate-slide-up">
    <form method="POST" action="{{ route('register') }}" class="space-y-6" id="registerForm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="space-y-2">
                <label for="first_name" class="form-label-modern">
                    <i class="ti ti-user text-blue-500 text-lg"></i>
                    نام
                </label>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required autofocus 
                       class="form-control-modern w-full px-4 py-3 md:px-5 md:py-4 rounded-xl md:rounded-2xl outline-none" placeholder="مثال: علی" maxlength="50">
                @error('first_name')
                    <p class="mt-2 text-xs text-rose-500 font-bold flex items-center gap-1 animate-shake">
                        <i class="ti ti-alert-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="last_name" class="form-label-modern">
                    <i class="ti ti-user text-blue-500 text-lg"></i>
                    نام خانوادگی
                </label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required 
                       class="form-control-modern w-full px-4 py-3 md:px-5 md:py-4 rounded-xl md:rounded-2xl outline-none" placeholder="مثال: محمدی" maxlength="50">
                @error('last_name')
                    <p class="mt-2 text-xs text-rose-500 font-bold flex items-center gap-1 animate-shake">
                        <i class="ti ti-alert-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div class="space-y-2">
            <label for="phone" class="form-label-modern">
                <i class="ti ti-phone text-blue-500 text-lg"></i>
                شماره موبایل
            </label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required 
                   class="form-control-modern w-full px-4 py-3 md:px-5 md:py-4 rounded-xl md:rounded-2xl outline-none text-left dir-ltr" placeholder="09xxxxxxxxx" pattern="09[0-9]{9}">
            @error('phone')
                <p class="mt-2 text-xs text-rose-500 font-bold flex items-center gap-1 animate-shake">
                    <i class="ti ti-alert-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="space-y-2">
                <label for="password" class="form-label-modern">
                    <i class="ti ti-lock text-blue-500 text-lg"></i>
                    رمز عبور
                </label>
                <div class="relative group">
                    <input type="password" id="password" name="password" required 
                           class="form-control-modern w-full pl-4 pr-12 py-3 md:py-4 rounded-xl md:rounded-2xl outline-none text-left dir-ltr" placeholder="••••••••">
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors focus:outline-none toggle-password" data-target="password">
                        <i class="ti ti-eye text-xl"></i>
                    </button>
                </div>
                
                <div id="password-strength" class="strength-meter hidden">
                    <div class="strength-segment segment-1"></div>
                    <div class="strength-segment segment-2"></div>
                    <div class="strength-segment segment-3"></div>
                </div>
                <p id="password-text" class="text-[10px] font-bold mt-1 text-slate-500 hidden"></p>
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="form-label-modern">
                    <i class="ti ti-lock-check text-blue-500 text-lg"></i>
                    تکرار رمز عبور
                </label>
                <div class="relative group">
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           class="form-control-modern w-full pl-4 pr-12 py-3 md:py-4 rounded-xl md:rounded-2xl outline-none text-left dir-ltr" placeholder="••••••••">
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors focus:outline-none toggle-password" data-target="password_confirmation">
                        <i class="ti ti-eye text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        @error('password')
            <p class="mt-2 text-xs text-rose-500 font-bold flex items-center gap-1 animate-shake">
                <i class="ti ti-alert-circle"></i>
                {{ $message }}
            </p>
        @enderror

        <div class="bg-blue-600/5 p-4 rounded-2xl border border-blue-500/10 mb-6">
            <p class="text-xs text-blue-400 leading-relaxed flex gap-2">
                <i class="ti ti-info-circle text-lg shrink-0"></i>
                رمز عبور باید حداقل ۸ کاراکتر باشد.
            </p>
        </div>

        <button type="submit" id="registerBtn" class="btn-modern w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center gap-3 text-lg shadow-lg shadow-blue-900/10 group">
            <i class="ti ti-user-plus text-xl group-hover:translate-x-1 transition-transform"></i>
            <span>ایجاد حساب کاربری</span>
        </button>
    </form>
    
    <div class="mt-8 pt-8 border-t border-slate-800 text-center">
        <p class="text-slate-400 font-medium">
            قبلاً ثبت‌نام کرده‌اید؟
            <a href="{{ route('login') }}" class="text-blue-500 font-black hover:text-blue-400 transition-colors mr-1">وارد شوید</a>
        </p>
    </div>
</div>
@endsection

@include('auth.register-scripts')
