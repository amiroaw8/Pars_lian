@extends('layouts.auth')

@section('title', 'ورود به سیستم - پارس لیان')

@section('content')
<div class="text-center mb-8 md:mb-10">
    <h1 class="text-2xl md:text-3xl font-black text-white mb-2 md:mb-3">خوش آمدید</h1>
    <p class="text-slate-500 font-medium text-sm md:text-base">لطفاً برای ورود به پنل خود اطلاعات را وارد کنید</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold flex items-center gap-2">
        <i class="ti ti-check-circle text-lg"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-700 text-sm font-bold flex items-center gap-2">
        <i class="ti ti-alert-circle text-lg"></i>
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-6">
    @csrf
    
    <div class="space-y-2">
        <label for="phone" class="form-label-modern text-slate-700">
            <i class="ti ti-phone text-blue-600 text-lg"></i>
            شماره تلفن
        </label>
        <div class="relative group">
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required autofocus 
                   class="form-control-modern w-full px-4 py-3 md:px-5 md:py-4 rounded-xl md:rounded-2xl outline-none text-left dir-ltr bg-white border-slate-200 text-slate-900" 
                   placeholder="09xxxxxxxxx" pattern="09[0-9]{9}">
            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
            </div>
        </div>
        @error('phone')
            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1 animate-shake">
                <i class="ti ti-alert-circle"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="password" class="form-label-modern text-slate-700">
            <i class="ti ti-lock text-blue-600 text-lg"></i>
            رمز عبور
        </label>
        <div class="relative group">
            <input type="password" id="password" name="password" required 
                   class="form-control-modern w-full pl-4 pr-12 py-3 md:py-4 rounded-xl md:rounded-2xl outline-none bg-white border-slate-200 text-slate-900 text-left dir-ltr" 
                   placeholder="••••••••">
            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors focus:outline-none toggle-password" data-target="password">
                <i class="ti ti-eye text-xl"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-2 text-xs text-rose-600 font-bold flex items-center gap-1 animate-shake">
                <i class="ti ti-alert-circle"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="flex items-center justify-between pt-2">
        <label class="flex items-center gap-2 cursor-pointer group">
            <div class="relative flex items-center">
                <input type="checkbox" name="remember" class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-slate-300 bg-white checked:bg-blue-600 checked:border-blue-600 transition-all">
                <i class="ti ti-check absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100 text-xs transition-opacity"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-slate-700 transition-colors">مرا به خاطر بسپار</span>
        </label>
        
        <a href="{{ route('password.request') }}" class="text-sm font-bold text-blue-600 hover:text-blue-500 transition-colors">
            فراموشی رمز عبور؟
        </a>
    </div>

    <button type="submit" id="loginBtn" class="btn-modern w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center gap-3 text-lg shadow-lg shadow-blue-900/10 group">
        <i class="ti ti-login text-xl group-hover:translate-x-1 transition-transform"></i>
        <span>ورود به سیستم</span>
    </button>
</form>

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <i class="ti ti-loader-2 animate-spin text-xl"></i>
            <span>در حال ورود...</span>
        `;
    });

    // Password Visibility Toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        });
    });
</script>
@endpush

<div class="mt-10 pt-8 border-t border-slate-100 text-center">
    <p class="text-slate-500 font-medium">
        حساب کاربری ندارید؟
        <a href="{{ route('register') }}" class="text-blue-600 font-black hover:text-blue-500 transition-colors mr-1">ثبت‌نام کنید</a>
    </p>
</div>
@endsection
