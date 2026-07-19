@extends('layouts.app')

@section('title', '۵۰۰ - خطای سرور')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="mb-8">
        <i class="ti ti-settings text-9xl text-amber-500 animate-spin-slow"></i>
    </div>
    <h1 class="text-4xl font-black text-gray-800 mb-4">خطای داخلی سرور</h1>
    <p class="text-gray-500 text-lg mb-10 max-w-md mx-auto">متاسفیم، مشکلی در سمت سرور پیش آمده است. تیم فنی ما در حال بررسی موضوع است.</p>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="window.location.reload()" class="flex items-center gap-2 px-8 py-3 bg-amber-100 text-amber-700 rounded-2xl hover:bg-amber-200 transition-all font-bold">
            <i class="ti ti-refresh"></i>
            تلاش مجدد
        </a>
        <a href="{{ url('/') }}" class="flex items-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all font-bold shadow-lg shadow-blue-200">
            <i class="ti ti-home"></i>
            صفحه اصلی
        </a>
    </div>
</div>

<style>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endsection
