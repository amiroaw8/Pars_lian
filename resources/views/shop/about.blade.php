@extends('layouts.shop')

@section('title', 'درباره ما - پارس لیان')

@section('shop-content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-10 sm:p-14">
        <h1 class="text-3xl font-black text-gray-900 mb-6">درباره پارس لیان</h1>
        <div class="prose prose-slate max-w-none text-gray-600 leading-8 space-y-4">
            <p>فروشگاه پارس لیان با تمرکز بر فروش قطعات کامپیوتر، لوازم جانبی و خدمات فنی، تلاش می‌کند تجربه‌ای مطمئن و سریع برای مشتریان فراهم کند.</p>
            <p>ما با تیم پشتیبانی فعال، گارانتی معتبر و امکان پیگیری سفارش، همراه شما از انتخاب محصول تا تحویل هستیم.</p>
            <ul class="list-disc pr-6 space-y-2">
                <li>ارائه محصولات اصل و باکیفیت</li>
                <li>پشتیبانی فنی و مشاوره خرید</li>
                <li>ارسال به سراسر کشور و تحویل حضوری در خرم‌آباد</li>
            </ul>
        </div>
        <div class="mt-10">
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-colors">
                مشاهده محصولات
                <i class="ti ti-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
@endsection
