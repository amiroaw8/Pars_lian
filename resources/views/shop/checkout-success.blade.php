@extends('layouts.shop')

@section('title', 'ثبت موفق سفارش - پارس لیان')

@section('shop-content')
<div class="py-20 bg-gray-50 min-h-screen flex items-center justify-center" dir="rtl">
    <div class="max-w-3xl w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[3rem] p-12 sm:p-20 text-center border border-gray-100 shadow-xl shadow-blue-900/5">
            <div class="w-32 h-32 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-10 animate-bounce" style="animation-duration: 3s">
                <i class="ti ti-circle-check text-7xl"></i>
            </div>
            
            <h1 class="text-4xl font-black text-gray-900 mb-6">سفارش شما با موفقیت ثبت شد!</h1>
            <p class="text-gray-500 mb-10 text-lg leading-relaxed">
                ممنون از اعتماد شما. شماره سفارش شما <span class="text-blue-600 font-black tracking-widest">{{ $order->order_number }}</span> می‌باشد.
                کارشناسان ما به زودی برای هماهنگی‌های بعدی با شما تماس خواهند گرفت.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg mx-auto mb-12">
                <div class="p-6 bg-gray-50 rounded-2xl text-right">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest block mb-2">مبلغ پرداخت شده</span>
                    <span class="text-xl font-black text-gray-900">{{ number_format($order->total) }} تومان</span>
                </div>
                <div class="p-6 bg-gray-50 rounded-2xl text-right">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest block mb-2">روش پرداخت</span>
                    <span class="text-xl font-black text-gray-900">
                        @if($order->payment_method === 'cod') پرداخت در محل
                        @else آنلاین @endif
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ route('customer.orders') }}" class="w-full sm:w-auto px-10 py-5 bg-gray-900 text-white rounded-2xl font-black hover:bg-gray-800 transition-all shadow-xl">
                    پیگیری سفارشات
                </a>
                <a href="{{ route('shop.index') }}" class="w-full sm:w-auto px-10 py-5 bg-white border-2 border-gray-100 text-gray-900 rounded-2xl font-black hover:bg-gray-50 transition-all">
                    بازگشت به فروشگاه
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
