@extends('layouts.app')

@section('title', 'فاکتورها و پرداخت‌ها')
@section('page_title', 'لیست فاکتورها و تراکنش‌ها')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-emerald-500/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-receipt text-amber-400"></i>
                        مدیریت مالی
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">فاکتورها و پرداخت‌ها</h2>
                    <p class="text-emerald-100 text-lg font-medium max-w-xl leading-relaxed">در این بخش می‌توانید تاریخچه تمامی پرداخت‌ها و فاکتورهای صادر شده خود را مشاهده و پیگیری کنید.</p>
                </div>
                <div class="flex flex-shrink-0">
                    <div class="w-24 h-24 md:w-40 md:h-40 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/30 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500">
                        <i class="ti ti-wallet text-6xl md:text-8xl drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/20 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-emerald-400/30 transition-colors duration-700"></div>
        </div>

        <x-enhanced-card title="فاکتورهای صادر شده" icon="receipt-2" class="animate-slide-up">
            <x-slot name="actions">
                <a href="{{ route('customer.dashboard') }}" class="btn-modern btn-modern-light py-2 px-6 text-sm group">
                    <i class="ti ti-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    <span>بازگشت به داشبورد</span>
                </a>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-2">شماره فاکتور</th>
                            <th class="px-6 py-2">مربوط به سفارش</th>
                            <th class="px-6 py-2">مبلغ کل (تومان)</th>
                            <th class="px-6 py-2">روش پرداخت</th>
                            <th class="px-6 py-2">تاریخ پرداخت</th>
                            <th class="px-6 py-2 text-center">وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr class="group hover:bg-slate-50/80 transition-all duration-500">
                            <td class="px-6 py-5 first:rounded-r-[2rem] bg-white group-hover:bg-transparent transition-colors">
                                <span class="font-black text-slate-900 bg-slate-100 px-4 py-2 rounded-xl group-hover:bg-white transition-colors"><x-hash-ref :value="$invoice->id" /></span>
                            </td>
                            <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                @if($invoice->order)
                                    <a href="{{ route('customer.orders.show', $invoice->order) }}" class="flex items-center gap-4 group/link">
                                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover/link:scale-110 group-hover/link:bg-blue-600 group-hover/link:text-white transition-all duration-500 shadow-sm">
                                            <i class="ti ti-device-laptop text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm group-hover/link:text-blue-600 transition-colors">{{ $invoice->order->device->type ?? 'دستگاه' }}</div>
                                            <div class="text-[10px] text-slate-400 font-black uppercase tracking-wider">سفارش <x-hash-ref :value="$invoice->order->id" /></div>
                                        </div>
                                    </a>
                                @else
                                    <div class="flex items-center gap-4 text-slate-300">
                                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100">
                                            <i class="ti ti-minus text-xl"></i>
                                        </div>
                                        <span class="text-sm font-bold">بدون سفارش</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-lg font-black text-emerald-600">{{ number_format($invoice->amount) }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">تومان</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-white transition-colors">
                                        <i class="ti ti-credit-card text-lg"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600">{{ $invoice->payment_method ?? 'نامشخص' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">
                                        @if($invoice->transaction_date)
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($invoice->transaction_date)->format('Y/m/d') }}
                                            @else
                                                {{ $invoice->transaction_date->format('Y/m/d') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </span>
                                    @if($invoice->transaction_date)
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $invoice->transaction_date->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 last:rounded-l-[2rem] bg-white group-hover:bg-transparent transition-colors text-center">
                                <span class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100/50 shadow-sm shadow-emerald-500/5 group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-all duration-300">
                                    <i class="ti ti-circle-check text-sm"></i>
                                    پرداخت شده
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-8 max-w-md mx-auto">
                                    <div class="relative">
                                        <div class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center text-slate-300 animate-pulse">
                                            <i class="ti ti-receipt-off text-6xl"></i>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-12 h-12 bg-white rounded-2xl shadow-xl flex items-center justify-center text-emerald-500 animate-bounce">
                                            <i class="ti ti-cash text-2xl"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="text-slate-900 font-black text-2xl">تراکنشی یافت نشد!</div>
                                        <p class="text-slate-500 text-sm font-medium leading-relaxed">هنوز هیچ فاکتوری برای شما صادر نشده است. پس از اتمام مراحل تعمیر، فاکتور نهایی در این بخش قابل مشاهده خواهد بود.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
            <div class="mt-10 pt-10 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
            @endif
        </x-enhanced-card>

        <!-- Help Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                <div class="relative z-10 flex items-start gap-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="ti ti-headset text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-2">نیاز به راهنمایی دارید؟</h4>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed mb-4">در صورت وجود هرگونه ابهام در فاکتورهای صادر شده یا تراکنش‌های بانکی، تیم پشتیبانی ما آماده پاسخگویی به شماست.</p>
                        <a href="{{ route('tracking.index') }}" class="text-blue-600 font-black text-sm flex items-center gap-2 hover:gap-4 transition-all">
                            تماس با پشتیبانی
                            <i class="ti ti-arrow-left"></i>
                        </a>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                <div class="relative z-10 flex items-start gap-6">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="ti ti-shield-check text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-2">امنیت پرداخت</h4>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed mb-4">تمامی تراکنش‌های مالی در سامانه پارس لیان از طریق درگاه‌های امن بانکی انجام شده و دارای کد پیگیری معتبر می‌باشند.</p>
                        <div class="flex items-center gap-4 grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-500">
                            <i class="ti ti-lock-square-rounded text-2xl text-slate-400"></i>
                            <i class="ti ti-credit-card-pay text-2xl text-slate-400"></i>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        </div>
    </div>
</div>
@endsection
