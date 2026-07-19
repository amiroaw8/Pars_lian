@extends('layouts.app')

@section('title', 'تایید دو مرحله‌ای - پارس لیان')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-slate-100 animate-slide-up">
        <div>
            <div class="mx-auto h-16 w-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mb-6">
                <i class="ti ti-shield-lock text-4xl"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-slate-900">
                تایید دو مرحله‌ای
            </h2>
            <p class="mt-4 text-center text-sm text-slate-500 leading-relaxed">
                یک کد تایید ۶ رقمی به شماره همراه 
                <span class="font-bold text-slate-700">{{ auth()->user()->phone }}</span> 
                ارسال شد. لطفاً آن را در کادر زیر وارد کنید.
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('verify.store') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="group relative">
                    <label for="two_factor_code" class="sr-only">کد تایید</label>
                    <input id="two_factor_code" name="two_factor_code" type="text" required 
                           class="appearance-none rounded-2xl relative block w-full px-4 py-4 border border-slate-200 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all sm:text-lg text-center tracking-[1em] font-bold" 
                           placeholder="000000" maxlength="6" autofocus>
                </div>
            </div>

            @if(session('warning'))
                <div class="text-amber-600 text-xs text-center font-medium mt-2 bg-amber-50 rounded-xl py-2 px-3">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('success'))
                <div class="text-emerald-600 text-xs text-center font-medium mt-2 bg-emerald-50 rounded-xl py-2 px-3">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->has('two_factor_code'))
                <div class="text-rose-500 text-xs text-center font-medium mt-2">
                    {{ $errors->first('two_factor_code') }}
                </div>
            @endif

            <div class="flex flex-col gap-4">
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-2xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-xl shadow-primary-200 transition-all hover:-translate-y-1">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="ti ti-check text-xl opacity-50 group-hover:opacity-100"></i>
                    </span>
                    تایید و ادامه
                </button>
                
                <div class="text-center mt-4">
                    <a href="{{ route('verify.resend') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-500 transition-colors inline-flex items-center gap-1">
                        <i class="ti ti-reload"></i>
                        ارسال مجدد کد تایید
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-50 pt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-center text-xs text-slate-400 hover:text-rose-500 transition-colors font-medium">
                    انصراف و خروج از حساب
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
