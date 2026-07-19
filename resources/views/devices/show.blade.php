@extends('layouts.admin')

@section('title', 'مشاهده دستگاه - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-slate-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-device-laptop text-blue-400"></i>
                        جزئیات فنی دستگاه
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">{{ $device->model }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                        <span class="px-4 py-1.5 rounded-xl bg-white/10 backdrop-blur-md text-white border border-white/20 text-xs font-black uppercase tracking-widest">
                            {{ $device->type }}
                        </span>
                        <span class="px-4 py-1.5 rounded-xl bg-primary-500/20 backdrop-blur-md text-primary-300 border border-primary-500/30 text-xs font-black">
                            ID: <x-hash-ref :value="$device->id" />
                        </span>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('automation.devices.edit', $device) }}" class="btn-modern btn-modern-warning py-4 px-8 shadow-xl shadow-amber-500/20 group">
                        <i class="ti ti-edit group-hover:rotate-12 transition-transform"></i>
                        <span>ویرایش اطلاعات</span>
                    </a>
                    <a href="{{ route('automation.devices.index') }}" class="btn-modern btn-modern-light py-4 px-8 group">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به لیست</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-blue-500/20 transition-colors duration-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Device Info Sidebar -->
            <div class="space-y-8">
                <x-enhanced-card variant="default" title="شناسنامه فنی" icon="id-badge-2" class="animate-slide-up">
                    <div class="flex flex-col items-center justify-center py-10 mb-8 rounded-[2rem] bg-gradient-to-br from-slate-50 to-blue-50/30 border border-slate-100 shadow-inner group/icon">
                        <div class="w-24 h-24 rounded-[2rem] bg-white shadow-xl flex items-center justify-center text-primary-600 mb-6 border border-slate-100 group-hover/icon:scale-110 group-hover/icon:rotate-3 transition-all duration-500">
                            <i class="ti ti-{{ $device->type == 'laptop' ? 'device-laptop' : ($device->type == 'mobile' ? 'device-mobile' : 'device-desktop') }} text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-1">{{ $device->model }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $device->type }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="group/item flex justify-between items-center p-4 rounded-2xl hover:bg-slate-50 transition-all duration-300">
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-600 transition-colors">
                                    <i class="ti ti-user-circle text-xl"></i>
                                </div>
                                <span class="text-sm font-black">مالک دستگاه</span>
                            </div>
                            <a href="{{ route('automation.customers.show', $device->customer) }}" class="flex items-center gap-2 group/link">
                                <span class="text-sm font-black text-slate-900 group-hover/link:text-primary-600 transition-colors">{{ $device->customer->name }}</span>
                                <i class="ti ti-chevron-left text-xs text-slate-300 group-hover/link:text-primary-600 group-hover/link:-translate-x-1 transition-all"></i>
                            </a>
                        </div>
                        
                        <div class="group/item flex justify-between items-center p-4 rounded-2xl hover:bg-slate-50 transition-all duration-300">
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-600 transition-colors">
                                    <i class="ti ti-hash text-xl"></i>
                                </div>
                                <span class="text-sm font-black">شماره اموال</span>
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-mono text-[10px] font-black border border-slate-200/50">
                                {{ $device->asset_number ?? 'ثبت نشده' }}
                            </span>
                        </div>

                        <div class="group/item flex justify-between items-center p-4 rounded-2xl hover:bg-slate-50 transition-all duration-300">
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-600 transition-colors">
                                    <i class="ti ti-barcode text-xl"></i>
                                </div>
                                <span class="text-sm font-black">شماره سریال</span>
                            </div>
                            <span class="text-xs font-bold text-slate-600 font-mono">
                                {{ $device->serial_number ?? '---' }}
                            </span>
                        </div>

                        <div class="group/item flex justify-between items-center p-4 rounded-2xl hover:bg-slate-50 transition-all duration-300">
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover/item:bg-emerald-100 group-hover/item:text-emerald-600 transition-colors">
                                    <i class="ti ti-shield-check text-xl"></i>
                                </div>
                                <span class="text-sm font-black">وضعیت گارانتی</span>
                            </div>
                            @if($device->has_guarantee)
                                <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-black border border-emerald-100/50 shadow-sm">دارای گارانتی</span>
                            @else
                                <span class="px-3 py-1 rounded-xl bg-slate-50 text-slate-400 text-[10px] font-black border border-slate-100">بدون گارانتی</span>
                            @endif
                        </div>

                        <div class="group/item flex justify-between items-center p-4 rounded-2xl hover:bg-slate-50 transition-all duration-300">
                            <div class="flex items-center gap-3 text-slate-500">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover/item:bg-blue-100 group-hover/item:text-blue-600 transition-colors">
                                    <i class="ti ti-calendar-event text-xl"></i>
                                </div>
                                <span class="text-sm font-black">تاریخ ثبت سیستم</span>
                            </div>
                            <span class="text-xs font-black text-slate-700" dir="ltr">
                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($device->created_at)->format('Y/m/d') }}
                                @else
                                    {{ $device->created_at->format('Y/m/d') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-10 pt-8 border-t border-slate-100">
                        <a href="{{ route('automation.service-orders.create', ['customer_id' => $device->customer_id, 'device_id' => $device->id]) }}" class="btn-modern btn-modern-primary w-full justify-center py-4 shadow-xl shadow-primary-500/20 group">
                            <i class="ti ti-plus group-hover:rotate-90 transition-transform"></i>
                            <span>ثبت سفارش جدید</span>
                        </a>
                    </div>
                </x-enhanced-card>
            </div>

            <!-- Service History Table -->
            <div class="lg:col-span-2 space-y-8">
                <x-enhanced-card variant="default" title="سوابق خدمات و تعمیرات" icon="history" class="animate-slide-up" style="animation-delay: 0.1s">
                    <div class="table-container">
                        <x-enhanced-table>
                            <x-slot name="headers">
                                <th class="text-right">شناسه سفارش</th>
                                <th class="text-right">تاریخ پذیرش</th>
                                <th class="text-right">ایراد اعلامی</th>
                                <th class="text-right">وضعیت</th>
                                <th class="text-right">هزینه نهایی</th>
                                <th class="text-center">عملیات</th>
                            </x-slot>
                            
                            <x-slot name="rows">
                                @forelse($device->serviceOrders as $order)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                    <td class="px-6 py-4">
                                        <span class="font-black text-slate-900 bg-slate-100 px-3 py-1.5 rounded-xl group-hover:bg-white transition-colors"><x-hash-ref :value="$order->id" /></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-700" dir="ltr">
                                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('Y/m/d') }}
                                                @else
                                                    {{ $order->created_at->format('Y/m/d') }}
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">{{ $order->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-[200px] truncate text-sm font-medium text-slate-600" title="{{ $order->fault }}">
                                            {{ $order->fault }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-enhanced-status-badge :status="$order->status" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="cost-value text-sm font-black text-emerald-600">
                                                {{ number_format($order->total_cost) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">تومان</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('automation.service-orders.show', $order) }}" class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 inline-flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all shadow-sm shadow-primary-100" title="مشاهده جزئیات سفارش">
                                            <i class="ti ti-eye text-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-20">
                                        <div class="flex flex-col items-center justify-center max-w-xs mx-auto opacity-40 group hover:opacity-100 transition-opacity">
                                            <div class="w-20 h-20 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-300 mb-6 group-hover:scale-110 group-hover:bg-blue-50 group-hover:text-blue-400 transition-all duration-500">
                                                <i class="ti ti-clipboard-off text-5xl"></i>
                                            </div>
                                            <h4 class="text-lg font-black text-slate-900 mb-2">فاقد سابقه تعمیراتی</h4>
                                            <p class="text-slate-500 text-xs font-medium leading-relaxed">تاکنون هیچ سفارش تعمیری برای این دستگاه در سامانه ثبت نشده است.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </x-slot>
                        </x-enhanced-table>
                    </div>
                </x-enhanced-card>

                <!-- Helpful Tips -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden relative">
                        <div class="relative z-10 flex items-start gap-5">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                <i class="ti ti-tool text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-md font-black text-slate-900 mb-2">نگهداری پیشگیرانه</h4>
                                <p class="text-slate-500 text-xs font-medium leading-relaxed">سرویس دوره‌ای دستگاه می‌تواند تا ۴۰٪ از هزینه‌های تعمیرات احتمالی در آینده بکاهد.</p>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden relative">
                        <div class="relative z-10 flex items-start gap-5">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                <i class="ti ti-history text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-md font-black text-slate-900 mb-2">تاریخچه خدمات</h4>
                                <p class="text-slate-500 text-xs font-medium leading-relaxed">تمامی قطعات تعویض شده در سوابق بالا ثبت شده و دارای مهلت تست و گارانتی می‌باشند.</p>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format costs to Persian locale
    const costs = document.querySelectorAll('.cost-value');
    const formatter = new Intl.NumberFormat('fa-IR');
    
    costs.forEach(el => {
        const val = parseInt(el.textContent.trim());
        if (!isNaN(val)) {
            el.textContent = formatter.format(val);
        }
    });
});
</script>
@endpush
@endsection
