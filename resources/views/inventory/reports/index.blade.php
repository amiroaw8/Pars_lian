@extends('layouts.admin')

@section('title', 'گزارش‌های انبار')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-slate-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-chart-pie text-primary-400"></i>
                        مرکز گزارش‌گیری
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">گزارش‌های جامع انبار</h2>
                    <p class="text-slate-400 text-lg font-medium max-w-2xl">دسترسی به آمار و اطلاعات دقیق ورود و خروج کالاها، ترازنامه مالی و کاردکس تعدادی</p>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('automation.inventory.index') }}" class="btn-modern btn-modern-light py-4 px-8 group">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به انبار</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Balance Report Card -->
            <a href="{{ route('automation.inventory.reports.balance') }}" class="group relative overflow-hidden rounded-[2.5rem] bg-white border border-slate-100 p-8 hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-500 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-50 rounded-bl-[2.5rem] -z-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="mb-6 w-16 h-16 bg-primary-500 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-primary-500/30 group-hover:rotate-6 transition-transform duration-300">
                    <i class="ti ti-scale"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-3 group-hover:text-primary-600 transition-colors">ترازنامه انبار</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">مشاهده وضعیت کلی موجودی کالاها، ورودی و خروجی در بازه زمانی مشخص به صورت تجمیعی.</p>
                <div class="flex items-center text-primary-600 text-sm font-bold">
                    <span>مشاهده گزارش</span>
                    <i class="ti ti-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Cardex Report Card -->
            <a href="{{ route('automation.inventory.reports.cardex') }}" class="group relative overflow-hidden rounded-[2.5rem] bg-white border border-slate-100 p-8 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-[2.5rem] -z-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="mb-6 w-16 h-16 bg-indigo-500 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-indigo-500/30 group-hover:rotate-6 transition-transform duration-300">
                    <i class="ti ti-file-analytics"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors">کاردکس کالا</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">ریز گردش یک کالای خاص با جزئیات کامل تاریخ، نوع تراکنش، مقدار وارده/صادره و مانده لحظه‌ای.</p>
                <div class="flex items-center text-indigo-600 text-sm font-bold">
                    <span>مشاهده گزارش</span>
                    <i class="ti ti-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Transactions Report Card -->
            <a href="{{ route('automation.inventory.reports.transactions') }}" class="group relative overflow-hidden rounded-[2.5rem] bg-white border border-slate-100 p-8 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-bl-[2.5rem] -z-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="mb-6 w-16 h-16 bg-amber-500 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-amber-500/30 group-hover:rotate-6 transition-transform duration-300">
                    <i class="ti ti-list-details"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-3 group-hover:text-amber-600 transition-colors">لیست تراکنش‌ها</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">لیست کامل کلیه حواله‌ها و رسیدهای انبار با قابلیت فیلتر پیشرفته بر اساس تاریخ، کاربر و نوع عملیات.</p>
                <div class="flex items-center text-amber-600 text-sm font-bold">
                    <span>مشاهده گزارش</span>
                    <i class="ti ti-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection