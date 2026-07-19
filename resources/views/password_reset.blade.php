@extends('layouts.app')

@section('title', 'تغییر رمز عبور')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100">
        <div class="text-center mb-8">
            <i class="ti ti-lock text-primary-600 text-5xl mb-4 block"></i>
            <h3 class="text-lg font-black text-slate-800">تغییر رمز عبور</h3>
            <p class="text-sm text-slate-500 mt-2">رمز عبور جدید را انتخاب کنید</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-600 text-sm">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="phone" value="{{ $phone }}">

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2 text-slate-700">رمز عبور جدید</label>
                <div class="relative">
                    <input type="password" name="password" required minlength="8" class="w-full p-3 border-2 border-slate-200 rounded-lg focus:border-primary-500 focus:outline-none @error('password') border-rose-500 @enderror">
                    <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                @error('password')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
                <p class="text-xs text-slate-500 mt-2">حداقل ۸ کاراکتر</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2 text-slate-700">تأیید رمز عبور</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" required minlength="8" class="w-full p-3 border-2 border-slate-200 rounded-lg focus:border-primary-500 focus:outline-none">
                    <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>

            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition-colors duration-200">
                <i class="ti ti-check mr-2"></i>
                تغییر رمز عبور
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                بازگشت به ورود
            </a>
        </div>
    </div>
</div>
@endsection

