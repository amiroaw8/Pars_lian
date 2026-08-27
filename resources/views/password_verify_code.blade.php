@extends('layouts.app')

@section('title', 'تأیید کد بازیابی')
@section('robots', 'noindex, follow')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100">
        <div class="text-center mb-8">
            <i class="ti ti-shield-check text-emerald-600 text-5xl mb-4 block"></i>
            <h3 class="text-lg font-black text-slate-800">تأیید کد</h3>
            <p class="text-sm text-slate-500 mt-2">کد تایید ۶ رقمی را که دریافت کردید وارد کنید</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-600 text-sm">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify-code') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-bold mb-2 text-slate-700">کد تایید</label>
                <input type="text" name="code" placeholder="000000" required maxlength="6" class="w-full p-3 border-2 border-slate-200 rounded-lg focus:border-primary-500 focus:outline-none text-center text-2xl tracking-widest @error('code') border-rose-500 @enderror" inputmode="numeric">
                @error('code')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <input type="hidden" name="phone" value="{{ $phone }}">

            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition-colors duration-200">
                <i class="ti ti-check mr-2"></i>
                تأیید کد
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-600">
            <p>کد دریافت نکردید؟ <a href="{{ route('password.request') }}" class="text-primary-600 hover:text-primary-700 font-bold">دوباره ارسال کنید</a></p>
        </div>
    </div>
</div>
@endsection
