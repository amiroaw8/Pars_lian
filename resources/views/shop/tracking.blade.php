@extends('layouts.shop')

@section('title', 'پیگیری سفارش - پارس لیان')

@section('shop-content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gray-900 px-8 py-6 text-white text-center">
            <h1 class="text-2xl font-black">پیگیری وضعیت سفارش</h1>
            <p class="text-sm text-gray-300 mt-2">سامانه خدمات و فروشگاه پارس لیان</p>
        </div>

        <div class="p-8">
            @if($error)
                <div class="bg-red-50 border border-red-100 text-red-700 rounded-2xl p-4 mb-6 flex items-start gap-3">
                    <i class="ti ti-alert-circle text-xl shrink-0"></i>
                    <p class="text-sm font-bold">{{ $error }}</p>
                </div>
            @endif

            @if($trackingType === 'service' && $serviceOrder)
                <div class="space-y-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-50 mb-4">
                            <i class="ti ti-{{ $serviceOrder->status->icon() ?? 'tool' }} text-3xl text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 mb-2">{{ $serviceOrder->status->label() }}</h2>
                        <p class="text-gray-500 font-bold">شماره سفارش تعمیر: <x-hash-ref :value="$serviceOrder->id" /></p>
                    </div>

                    @if($serviceOrder->device)
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">مشخصات دستگاه</h3>
                        <div class="bg-gray-50 rounded-2xl p-5 space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">نوع:</span><span class="font-bold">{{ $serviceOrder->device->type ?? 'نامشخص' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">مدل:</span><span class="font-bold">{{ $serviceOrder->device->model }}</span></div>
                        </div>
                    </div>
                    @endif

                    @if($serviceOrder->orderLogs->isNotEmpty())
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">تاریخچه</h3>
                        <ul class="space-y-4">
                            @foreach($serviceOrder->orderLogs as $log)
                                @if($log->action === 'status_change')
                                <li class="flex justify-between gap-4 text-sm border-r-2 border-blue-200 pr-4">
                                    <span class="text-gray-600">{{ $log->description }}</span>
                                    <span class="text-gray-400 whitespace-nowrap">{{ $log->created_at->format('Y/m/d H:i') }}</span>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            @elseif($trackingType === 'shop' && $shopOrder)
                <div class="space-y-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 mb-4">
                            <i class="ti ti-package text-3xl text-emerald-600"></i>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 mb-2">{{ $shopOrder->status->label() }}</h2>
                        <p class="text-gray-500 font-bold">شماره سفارش: {{ $shopOrder->order_number ?? '#'.$shopOrder->id }}</p>
                        @if($shopOrder->tracking_code)
                            <p class="text-sm text-blue-600 font-bold mt-2">کد رهگیری پست: {{ $shopOrder->tracking_code }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">اقلام سفارش</h3>
                        <div class="space-y-3">
                            @foreach($shopOrder->items as $item)
                            <div class="flex justify-between items-center bg-gray-50 rounded-xl p-4 text-sm">
                                <span class="font-bold text-gray-800">{{ $item->product_name ?? $item->product?->name ?? 'کالا' }}</span>
                                <span class="text-gray-500">{{ $item->quantity }} × {{ number_format($item->price) }} تومان</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-left font-black text-lg text-gray-900">
                            جمع کل: {{ number_format($shopOrder->total) }} تومان
                        </div>
                    </div>
                </div>
            @else
                <form action="{{ route('tracking.index') }}" method="GET" class="space-y-6">
                    <div>
                        <label for="tracking_id" class="block text-sm font-black text-gray-700 mb-2">شماره سفارش / کد پیگیری</label>
                        <input type="text" name="tracking_id" id="tracking_id" value="{{ request('tracking_id') }}"
                               class="w-full rounded-2xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 font-bold px-4 py-3"
                               placeholder="مثال: 1024 یا PL-12345" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-black text-gray-700 mb-2">شماره موبایل</label>
                        <input type="tel" name="phone" id="phone" value="{{ request('phone') }}"
                               class="w-full rounded-2xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 font-bold px-4 py-3"
                               placeholder="مثال: 09123456789" required>
                    </div>
                    <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-black hover:bg-blue-600 transition-colors">
                        پیگیری سفارش
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-gray-50 px-8 py-4 border-t text-center">
            <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:text-blue-800 font-bold">بازگشت به صفحه اصلی</a>
        </div>
    </div>
</div>
@endsection
