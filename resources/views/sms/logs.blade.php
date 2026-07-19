@extends('layouts.admin')

@section('title', 'تاریخچه پیامک‌ها - پارس لیان')

@section('content')
<div class="page-header animate-slide-up">
    <div>
        <h1 class="page-title text-gradient">
            <i class="ti ti-history"></i>
            تاریخچه پیامک‌ها
        </h1>
        <div class="breadcrumb text-secondary-600">
            <a href="/" class="hover:text-primary-600 transition-colors">
                <i class="ti ti-home"></i>
                خانه
            </a>
            <i class="ti ti-chevron-left"></i>
            <a href="{{ route('admin.sms.dashboard') }}" class="hover:text-primary-600 transition-colors">
                داشبورد SMS
            </a>
            <i class="ti ti-chevron-left"></i>
            <span>لاگ‌های سیستم</span>
        </div>
    </div>
    
    <div class="flex gap-3">
        <a href="{{ route('admin.sms.dashboard') }}" class="btn-modern btn-modern-light">
            <i class="ti ti-arrow-right"></i>
            بازگشت به داشبورد
        </a>
    </div>
</div>

<x-enhanced-card title="لیست تمامی پیامک‌های ارسالی" icon="list-details" animated>
    <x-enhanced-table :headers="['شناسه', 'گیرنده', 'متن پیام', 'وضعیت', 'جزئیات خطا', 'SMS ID', 'تاریخ و ساعت', 'سفارش']">
        @foreach($logs as $log)
        <tr class="group hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4">
                <span class="text-xs font-bold text-slate-400"><x-hash-ref :value="$log->id" /></span>
            </td>
            <td class="px-6 py-4">
                <span class="font-bold text-slate-700 dir-ltr inline-block">{{ $log->phone }}</span>
            </td>
            <td class="px-6 py-4">
                <div class="max-w-[300px] text-slate-600 text-sm leading-relaxed" title="{{ $log->message }}">
                    {{ $log->message }}
                </div>
            </td>
            <td class="px-6 py-4">
                @php
                    $statusType = $log->status == 'sent' ? 'success' : ($log->status == 'failed' ? 'danger' : ($log->status == 'error' ? 'danger' : 'warning'));
                    $statusLabel = $log->status == 'sent' ? 'ارسال شد' : ($log->status == 'failed' ? 'خطای پنل' : ($log->status == 'error' ? 'خطای سیستم' : 'در انتظار'));
                @endphp
                <x-enhanced-status-badge :status="$statusType" :label="$statusLabel" />
            </td>
            <td class="px-6 py-4">
                @if($log->error_message)
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded-md self-start">
                            {{ $log->error_code }}
                        </span>
                        <span class="text-xs text-slate-500 max-w-[150px] truncate" title="{{ $log->error_message }}">
                            {{ $log->error_message }}
                        </span>
                    </div>
                @else
                    <span class="text-slate-300 text-xs">---</span>
                @endif
            </td>
            <td class="px-6 py-4">
                <code class="text-xs bg-slate-100 px-2 py-1 rounded-lg text-slate-500 font-mono">
                    {{ $log->sms_id ?? '---' }}
                </code>
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

    <!-- Pagination -->
    <div class="mt-8">
        {{ $logs->links() }}
    </div>
</x-enhanced-card>
@endsection
