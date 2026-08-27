@extends('layouts.app')

@section('title', 'بازیابی رمز عبور')
@section('robots', 'noindex, follow')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 rounded-2xl shadow">
        <h3 class="text-lg font-black mb-4">درخواست تغییر رمز عبور</h3>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">ایمیل</label>
                <input type="email" name="email" required class="w-full p-3 border rounded" value="{{ old('email') }}">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded">ارسال لینک بازیابی</button>
        </form>
    </div>
</div>
@endsection
