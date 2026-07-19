@extends('layouts.app')

@section('title', 'راهنمای کامپوننت‌های مدرن - پارس لیان')

@section('content')
<div class="space-y-12 pb-20">
    <x-page-header 
        title="راهنمای کامپوننت‌های سیستم" 
        subtitle="مستندات و نمونه کدهای المان‌های رابط کاربری مدرن پلتفرم پارس لیان برای استفاده توسعه‌دهندگان."
        badge="Developer Guide"
        badgeIcon="ti-code"
        headerIcon="ti-book"
        class="mb-12"
    />

    <!-- Page Header Component -->
    <section class="space-y-6">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i class="ti ti-layout-header text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">هدر صفحات (Page Header)</h2>
        </div>
        
        <x-enhanced-card>
            <p class="text-slate-600 mb-6 leading-relaxed">
                این کامپوننت برای ایجاد هدرهای یکپارچه و جذاب در بالای تمامی صفحات اصلی استفاده می‌شود. این المان به صورت خودکار ریسپانسیو بوده و دارای انیمیشن‌های ورود است.
            </p>

            <div class="bg-slate-900 rounded-2xl p-6 mb-8 overflow-x-auto">
                <pre class="text-blue-400 font-mono text-sm"><code>&lt;x-page-header 
    title="عنوان صفحه" 
    subtitle="توضیحات کوتاه در مورد محتوای این صفحه"
    badge="متن نشان"
    badgeIcon="ti-star"
    headerIcon="ti-settings"
    actionUrl="@{{ route('some.route') }}"
    actionText="دکمه عملیات"
    actionIcon="ti-plus"
/&gt;</code></pre>
            </div>

            <h3 class="font-bold text-slate-800 mb-4">ویژگی‌ها (Props):</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <code class="text-primary-600 font-bold">title</code>
                    <span class="text-slate-500 text-sm mr-2">(اجباری) - عنوان اصلی صفحه</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <code class="text-primary-600 font-bold">subtitle</code>
                    <span class="text-slate-500 text-sm mr-2">(اختیاری) - توضیحات زیر عنوان</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <code class="text-primary-600 font-bold">badge</code>
                    <span class="text-slate-500 text-sm mr-2">(اختیاری) - متن نشان بالای عنوان</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <code class="text-primary-600 font-bold">actionUrl</code>
                    <span class="text-slate-500 text-sm mr-2">(اختیاری) - آدرس دکمه عملیات اصلی</span>
                </div>
            </div>
        </x-enhanced-card>
    </section>

    <!-- Enhanced Card Component -->
    <section class="space-y-6">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="ti ti-square text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">کارت‌های پیشرفته (Enhanced Card)</h2>
        </div>
        
        <x-enhanced-card>
            <p class="text-slate-600 mb-6 leading-relaxed">
                برای نمایش محتوا در قالب باکس‌های مدرن با سایه‌های نرم و قابلیت‌های انیمیشن.
            </p>

            <div class="bg-slate-900 rounded-2xl p-6 mb-8 overflow-x-auto">
                <pre class="text-blue-400 font-mono text-sm"><code>&lt;x-enhanced-card 
    title="عنوان کارت" 
    icon="settings" 
    animated
&gt;
    محتوای کارت شما در اینجا قرار می‌گیرد
&lt;/x-enhanced-card&gt;</code></pre>
            </div>
        </x-enhanced-card>
    </section>

    <!-- Enhanced Table Component -->
    <section class="space-y-6">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i class="ti ti-table text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">جداول مدرن (Enhanced Table)</h2>
        </div>
        
        <x-enhanced-card>
            <p class="text-slate-600 mb-6 leading-relaxed">
                جداول کاملاً ریسپانسیو که در موبایل به صورت اسکرول‌شونده نمایش داده می‌شوند.
            </p>

            <div class="bg-slate-900 rounded-2xl p-6 mb-8 overflow-x-auto">
                <pre class="text-blue-400 font-mono text-sm"><code>&lt;x-enhanced-table&gt;
    &lt;x-slot name="headers"&gt;
        &lt;th&gt;ستون اول&lt;/th&gt;
        &lt;th&gt;ستون دوم&lt;/th&gt;
    &lt;/x-slot&gt;
    &lt;x-slot name="rows"&gt;
        &lt;tr&gt;
            &lt;td&gt;دیتا ۱&lt;/td&gt;
            &lt;td&gt;دیتا ۲&lt;/td&gt;
        &lt;/tr&gt;
    &lt;/x-slot&gt;
&lt;/x-enhanced-table&gt;</code></pre>
            </div>
        </x-enhanced-card>
    </section>

    <!-- Status Badge Component -->
    <section class="space-y-6">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i class="ti ti-tag text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">نشان‌های وضعیت (Status Badge)</h2>
        </div>
        
        <x-enhanced-card>
            <p class="text-slate-600 mb-6 leading-relaxed">
                برای نمایش وضعیت‌ها (در انتظار، در حال انجام، تکمیل شده و غیره) با رنگ‌بندی خودکار.
            </p>

            <div class="bg-slate-900 rounded-2xl p-6 mb-8 overflow-x-auto">
                <pre class="text-blue-400 font-mono text-sm"><code>&lt;x-enhanced-status-badge status="pending" /&gt;
&lt;x-enhanced-status-badge status="processing" /&gt;
&lt;x-enhanced-status-badge status="completed" /&gt;</code></pre>
            </div>
            
            <div class="flex flex-wrap gap-4 mt-4">
                <x-enhanced-status-badge status="pending" />
                <x-enhanced-status-badge status="processing" />
                <x-enhanced-status-badge status="completed" />
                <x-enhanced-status-badge status="cancelled" />
            </div>
        </x-enhanced-card>
    </section>
</div>
@endsection
