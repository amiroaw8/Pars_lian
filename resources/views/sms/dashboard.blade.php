@extends('layouts.admin')

@section('title', 'داشبورد مدیریت SMS - پارس لیان')

@section('content')
<div class="page-header animate-slide-up">
    <div>
        <h1 class="page-title text-gradient">
            <i class="ti ti-message-dots"></i>
            مدیریت سامانه پیامک
        </h1>
        <div class="breadcrumb text-secondary-600">
            <a href="/" class="hover:text-primary-600 transition-colors">
                <i class="ti ti-home"></i>
                خانه
            </a>
            <i class="ti ti-chevron-left"></i>
            <span>داشبورد SMS</span>
        </div>
    </div>
</div>

<!-- آمار کلی -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-fade-in">
    <!-- کل پیامک‌ها -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition-all group overflow-hidden relative">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50/50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl relative z-10 group-hover:bg-blue-600 group-hover:text-white transition-colors">
            <i class="ti ti-messages"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-medium text-slate-500 mb-1 text-right">کل پیامک‌های ارسالی</p>
            <h3 class="text-2xl font-black text-slate-900 leading-none">{{ number_format($totalSMS) }}</h3>
        </div>
    </div>

    <!-- ارسال موفق -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition-all group overflow-hidden relative">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50/50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl relative z-10 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
            <i class="ti ti-circle-check"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-medium text-slate-500 mb-1 text-right">ارسال موفق</p>
            <h3 class="text-2xl font-black text-slate-900 leading-none">{{ number_format($sentSMS) }}</h3>
        </div>
    </div>

    <!-- ارسال ناموفق -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition-all group overflow-hidden relative">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50/50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-2xl relative z-10 group-hover:bg-rose-600 group-hover:text-white transition-colors">
            <i class="ti ti-circle-x"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-medium text-slate-500 mb-1 text-right">ارسال ناموفق</p>
            <h3 class="text-2xl font-black text-slate-900 leading-none">{{ number_format($failedSMS) }}</h3>
        </div>
    </div>

    <!-- موجودی حساب -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-5 hover:shadow-md transition-all group overflow-hidden relative">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50/50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl relative z-10 group-hover:bg-amber-600 group-hover:text-white transition-colors">
            <i class="ti ti-wallet"></i>
        </div>
        <div class="relative z-10">
            <p class="text-sm font-medium text-slate-500 mb-1 text-right">موجودی (پیامک)</p>
            <h3 class="text-2xl font-black text-slate-900 leading-none">
                {{ $balance ?? 'نامشخص' }}
            </h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- فرم ارسال تست -->
    <div class="lg:col-span-1">
        <x-enhanced-card title="ارسال پیامک تستی" icon="send" animated>
            <form action="{{ route('admin.sms.send-test') }}" method="POST" class="space-y-6">
                @csrf
                <div class="form-group-modern group">
                    <label for="phone" class="form-label-modern group-focus-within:text-blue-600">
                        <i class="ti ti-phone text-lg"></i>
                        شماره موبایل
                    </label>
                    <input type="text" class="form-control-modern text-left dir-ltr" id="phone" name="phone" 
                           value="09111111111" required placeholder="09xxxxxxxxx">
                </div>

                <div class="form-group-modern group">
                    <label for="message" class="form-label-modern group-focus-within:text-blue-600">
                        <i class="ti ti-message-text text-lg"></i>
                        متن پیام
                    </label>
                    <textarea class="form-control-modern" id="message" name="message" rows="4" required placeholder="متن پیامک را اینجا وارد کنید...">تست سرویس SMS - سیستم پارس لیان</textarea>
                </div>

                <button type="submit" class="btn-modern btn-modern-primary w-full py-4 justify-center">
                    <i class="ti ti-send text-xl"></i>
                    <span>ارسال پیامک تست</span>
                </button>
            </form>
        </x-enhanced-card>
    </div>

    <!-- آخرین لاگ‌ها -->
    <div class="lg:col-span-2">
        <x-enhanced-card title="آخرین پیامک‌های ارسالی" icon="history" animated>
            <x-slot name="actions">
                <a href="{{ route('admin.sms.logs') }}" class="btn-modern btn-modern-light btn-sm">
                    <i class="ti ti-list-details"></i>
                    مشاهده همه
                </a>
            </x-slot>

            <x-enhanced-table :headers="['گیرنده', 'متن پیام', 'وضعیت', 'زمان ارسال', 'سفارش']">
                @foreach($recentLogs as $log)
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-slate-700 dir-ltr inline-block">{{ $log->phone }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-[200px] truncate text-slate-600 text-sm" title="{{ $log->message }}">
                            {{ $log->message }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusType = $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : ($log->status == 'error' ? 'danger' : 'warning'));
                            $statusLabel = $log->status == 'sent' ? 'ارسال شد' : ($log->status == 'failed' ? 'خطای پنل' : ($log->status == 'error' ? 'خطای سیستم' : 'در انتظار'));
                        @endphp
                        <div class="flex flex-col gap-1">
                            <x-enhanced-status-badge :status="$statusType" :label="$statusLabel" />
                            @if($log->error_message)
                                <span class="text-[10px] text-rose-500 font-bold truncate max-w-[100px]" title="{{ $log->error_message }}">
                                    {{ $log->error_message }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700">
                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($log->created_at)->format('Y/m/d') }}
                                @else
                                    {{ $log->created_at->format('Y/m/d') }}
                                @endif
                            </span>
                            <span class="text-xs text-slate-400">
                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($log->created_at)->format('H:i') }}
                                @else
                                    {{ $log->created_at->format('H:i') }}
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($log->serviceOrder)
                            <a href="{{ route('automation.service-orders.show', $log->serviceOrder) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-black hover:bg-blue-600 hover:text-white transition-all">
                                <i class="ti ti-hash text-sm"></i>
                                {{ $log->serviceOrder->id }}
                            </a>
                        @else
                            <span class="text-slate-400 text-xs italic">تستی / سیستم</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </x-enhanced-table>
        </x-enhanced-card>
    </div>
</div>
@endsection
