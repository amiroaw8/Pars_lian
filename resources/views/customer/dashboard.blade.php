@extends('layouts.app')

@section('title', 'پنل کاربری من')
@section('page_title', 'میز کار کاربری')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Welcome Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-blue-500/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-sparkles text-amber-400"></i>
                        خوش آمدید
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">سلام، {{ \Illuminate\Support\Facades\Auth::user()->name }} عزیز 👋</h2>
                    <p class="text-blue-100 text-lg font-medium max-w-xl leading-relaxed">در اینجا می‌توانید وضعیت تمامی سفارشات خود (تعمیرات و فروشگاه) را به صورت لحظه‌ای پیگیری کنید.</p>
                </div>
                <div class="flex flex-shrink-0">
                    <div class="w-24 h-24 md:w-40 md:h-40 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/30 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500">
                        <i class="ti ti-user-circle text-6xl md:text-8xl drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/20 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-blue-400/30 transition-colors duration-700"></div>
        </div>

        <!-- Profile Completion Banner -->
        @if($stats['profile_completion'] < 100)
        <div class="bg-white rounded-[2rem] border border-amber-100 shadow-sm p-6 relative overflow-hidden group animate-slide-up">
            <div class="flex flex-col md:flex-row items-center gap-6 relative z-10">
                <div class="w-20 h-20 rounded-2xl bg-amber-50 flex flex-col items-center justify-center text-amber-600 border border-amber-100 shrink-0">
                    <span class="text-2xl font-black">{{ $stats['profile_completion'] }}%</span>
                    <span class="text-[10px] font-bold uppercase">تکمیل شده</span>
                </div>
                <div class="flex-1 text-center md:text-right">
                    <h3 class="text-xl font-black text-slate-900 mb-2">پروفایل شما نیاز به تکمیل دارد!</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">
                        برای ثبت سفارشات جدید و استفاده از تمامی امکانات سایت، لطفاً اطلاعات کاربری و آدرس خود را تکمیل کنید.
                    </p>
                </div>
                <div class="shrink-0 w-full md:w-auto">
                    <a href="{{ route('customer.profile') }}" class="btn-modern w-full py-4 px-8 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl flex items-center justify-center gap-3 shadow-lg shadow-amber-900/20">
                        <span>تکمیل پروفایل</span>
                        <i class="ti ti-arrow-left text-xl"></i>
                    </a>
                </div>
            </div>
            <!-- Progress Bar Background -->
            <div class="absolute bottom-0 left-0 w-full h-1 bg-slate-100">
                <div class="h-full bg-amber-500 transition-all duration-1000" x-data="{ width: {{ $stats['profile_completion'] }} }" :style="'width: ' + width + '%'"></div>
            </div>
            <!-- Decorative Icon -->
            <i class="ti ti-alert-circle text-8xl text-amber-500/5 absolute -bottom-4 -left-4 -rotate-12"></i>
        </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="animate-slide-up" style="animation-delay: 0.1s">
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                    <div class="relative z-10 flex items-center gap-5">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                            <i class="ti ti-tool text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-slate-700 text-xs font-black uppercase tracking-widest mb-1">تعمیرات</div>
                            <div class="text-2xl font-black text-slate-900">{{ $stats['total_repair_orders'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="animate-slide-up" style="animation-delay: 0.2s">
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                    <div class="relative z-10 flex items-center gap-5">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                            <i class="ti ti-shopping-cart text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-slate-700 text-xs font-black uppercase tracking-widest mb-1">خریدها</div>
                            <div class="text-2xl font-black text-slate-900">{{ $stats['total_shop_orders'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="animate-slide-up" style="animation-delay: 0.3s">
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                    <div class="relative z-10 flex items-center gap-5">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 shadow-sm group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-500">
                            <i class="ti ti-hourglass-low text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-slate-700 text-xs font-black uppercase tracking-widest mb-1">در جریان</div>
                            <div class="text-2xl font-black text-slate-900">{{ $stats['pending_repairs'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="animate-slide-up" style="animation-delay: 0.4s">
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group overflow-hidden relative">
                    <div class="relative z-10 flex items-center gap-5">
                        <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                            <i class="ti ti-receipt text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-slate-700 text-xs font-black uppercase tracking-widest mb-1">کل پرداختی‌ها</div>
                            <div class="text-xl font-black text-slate-900">
                                {{ number_format($stats['total_payments']) }}
                                <small class="text-[10px] font-bold text-slate-400 mr-1">تومان</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- دکمه مشاهده سفارشات -->
        <div class="animate-slide-up" style="animation-delay: 0.45s">
            <a href="{{ route('customer.orders') }}" class="flex items-center justify-between w-full p-5 bg-gradient-to-l from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-[2rem] shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center group-hover:bg-white/30 transition-colors">
                        <i class="ti ti-list-details text-2xl"></i>
                    </div>
                    <div>
                        <div class="font-black text-lg">مشاهده همه سفارشات</div>
                        <div class="text-blue-200 text-xs font-medium">تعمیرات و خریدهای فروشگاه</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-black">{{ $stats['total_repair_orders'] + $stats['total_shop_orders'] }} سفارش</span>
                    <i class="ti ti-arrow-left text-xl group-hover:-translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Activity Sections -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Recent Shop Orders -->
                <x-enhanced-card title="سه خرید آخر فروشگاه" icon="shopping-cart" class="animate-slide-up" style="animation-delay: 0.5s">
                    <x-slot name="actions">
                        <a href="{{ route('customer.orders') }}" class="btn-modern btn-modern-light py-2 px-4 text-sm group">
                            <span>مشاهده همه</span>
                            <i class="ti ti-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    </x-slot>

                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                    <th class="px-4 py-2">شماره سفارش</th>
                                    <th class="px-4 py-2">وضعیت سفارش</th>
                                    <th class="px-4 py-2">وضعیت پرداخت</th>
                                    <th class="px-4 py-2 text-center">مبلغ</th>
                                    <th class="px-4 py-2 text-center">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shopOrders->take(3) as $order)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                    <td class="px-4 py-4 first:rounded-r-2xl bg-white group-hover:bg-transparent transition-colors">
                                        <span class="font-black text-slate-900 bg-slate-100 px-3 py-1.5 rounded-xl group-hover:bg-white transition-colors"><x-hash-ref :value="$order->order_number" /></span>
                                    </td>
                                    <td class="px-4 py-4 bg-white group-hover:bg-transparent transition-colors">
                                        <x-enhanced-status-badge :status="$order->status->value ?? $order->status" size="xs" />
                                    </td>
                                    <td class="px-4 py-4 bg-white group-hover:bg-transparent transition-colors">
                                        @php
                                            $paymentVariant = match($order->payment_status->value ?? $order->payment_status) {
                                                'paid'     => 'success-solid',
                                                'failed'   => 'danger',
                                                'refunded' => 'secondary',
                                                default    => 'warning',
                                            };
                                        @endphp
                                        <x-enhanced-status-badge
                                            :status="$order->payment_status->value ?? $order->payment_status"
                                            :label="$order->payment_status->label()"
                                            :variant="$paymentVariant"
                                            size="xs"
                                        />
                                    </td>
                                    <td class="px-4 py-4 text-center bg-white group-hover:bg-transparent transition-colors">
                                        <div class="text-sm font-black text-emerald-600">{{ number_format($order->total) }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center bg-white group-hover:bg-transparent transition-colors last:rounded-l-2xl">
                                        <a href="{{ route('customer.orders.shop-show', $order) }}" class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-500 mx-auto">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-8 text-center text-slate-400 text-xs">خرید جدیدی ثبت نشده است.</td></tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </x-enhanced-card>

                <!-- Recent Repair Orders -->
                <x-enhanced-card title="سه سفارش آخر تعمیرات" icon="tool" class="animate-slide-up" style="animation-delay: 0.6s">
                    <x-slot name="actions">
                        <a href="{{ route('customer.orders') }}" class="btn-modern btn-modern-light py-2 px-4 text-sm group">
                            <span>مشاهده همه</span>
                            <i class="ti ti-chevron-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    </x-slot>

                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                    <th class="px-4 py-2">کد پیگیری</th>
                                    <th class="px-4 py-2">دستگاه</th>
                                    <th class="px-4 py-2">وضعیت</th>
                                    <th class="px-4 py-2 text-center">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($repairOrders->take(3) as $order)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                    <td class="px-4 py-4 first:rounded-r-2xl bg-white group-hover:bg-transparent transition-colors">
                                        <span class="font-black text-slate-900 bg-slate-100 px-3 py-1.5 rounded-xl group-hover:bg-white transition-colors"><x-hash-ref :value="$order->id" /></span>
                                    </td>
                                    <td class="px-4 py-4 bg-white group-hover:bg-transparent transition-colors">
                                        <div class="text-sm font-bold text-slate-800">{{ $order->device->type ?? 'نامشخص' }}</div>
                                    </td>
                                    <td class="px-4 py-4 bg-white group-hover:bg-transparent transition-colors">
                                        <x-enhanced-status-badge :status="$order->status->value ?? $order->status" size="xs" />
                                    </td>
                                    <td class="px-4 py-4 text-center bg-white group-hover:bg-transparent transition-colors last:rounded-l-2xl">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all duration-500 mx-auto">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-8 text-center text-slate-400 text-xs">سفارش تعمیری یافت نشد.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-enhanced-card>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-8">
                <!-- Profile Summary -->
                <x-enhanced-card class="animate-slide-up" style="animation-delay: 0.7s">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-[2.5rem] flex items-center justify-center text-4xl font-black mb-6 shadow-xl shadow-blue-500/20">
                            {{ mb_substr(\Illuminate\Support\Facades\Auth::user()->name, 0, 1) }}
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-1">{{ \Illuminate\Support\Facades\Auth::user()->name }}</h3>
                        <p class="text-slate-400 text-sm font-bold mb-8 dir-ltr">{{ \Illuminate\Support\Facades\Auth::user()->phone }}</p>
                        
                        <div class="w-full space-y-3">
                            <a href="{{ route('customer.profile') }}" class="btn-modern btn-modern-primary w-full py-4 group">
                                <i class="ti ti-user-edit"></i>
                                <span>ویرایش پروفایل</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                @csrf
                                <button type="submit" class="btn-modern btn-modern-light w-full py-4 text-rose-600 hover:bg-rose-50 hover:border-rose-100 group">
                                    <i class="ti ti-logout"></i>
                                    <span>خروج از حساب</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </x-enhanced-card>

                <!-- Quick Support -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl animate-slide-up group" style="animation-delay: 0.8s">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 group-hover:bg-white group-hover:text-blue-600 transition-all duration-500">
                            <i class="ti ti-headset text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-black mb-2">نیاز به راهنمایی دارید؟</h4>
                        <p class="text-slate-400 text-sm font-medium mb-8 leading-relaxed">کارشناسان ما آماده پاسخگویی به سوالات شما در مورد وضعیت سفارشات هستند.</p>
                        <a href="tel:06633308603" class="flex items-center justify-between p-4 bg-white/5 hover:bg-white/10 rounded-2xl border border-white/10 transition-colors group/link">
                            <span class="font-black text-lg" dir="ltr">066 - 33308603</span>
                            <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center group-hover/link:scale-110 transition-transform">
                                <i class="ti ti-phone-call"></i>
                            </div>
                        </a>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
