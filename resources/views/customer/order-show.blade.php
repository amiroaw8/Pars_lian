@extends('layouts.app')

@section('title', 'سفارش تعمیر ' . hash_ref_plain($serviceOrder->id))
@section('page_title', 'جزئیات سفارش تعمیر')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/5 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-2/3 space-y-8">
                <!-- Order Info Card -->
                <x-enhanced-card icon="info-circle" class="animate-fade-in">
                    <x-slot name="title">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <span class="text-xl font-black text-slate-900">جزئیات سفارش <x-hash-ref :value="$serviceOrder->id" /></span>
                            <x-enhanced-status-badge :status="$serviceOrder->status->value ?? $serviceOrder->status" />
                        </div>
                    </x-slot>

                    <x-slot name="actions">
                        <button onclick="printOrder()" class="btn-modern btn-modern-primary py-2 px-6 text-sm group ml-2 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transition-all duration-300">
                            <i class="ti ti-printer text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="font-bold">چاپ فاکتور</span>
                        </button>
                        <a href="{{ route('customer.orders') }}" class="btn-modern btn-modern-light py-2 px-4 text-sm group">
                            <i class="ti ti-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            <span>بازگشت به لیست</span>
                        </a>
                    </x-slot>

                    @php
                        $device = $serviceOrder->device;
                        $deviceType = $device?->type ?? 'نامشخص';
                        $deviceModel = $device?->model ?? 'نامشخص';
                        $serialNumber = filled($device?->serial_number) ? $device->serial_number : null;
                        $assetNumber = filled($device?->asset_number) ? $device->asset_number : null;
                        $deviceNotRegistered = in_array($deviceType, ['عدم ثبت', 'unknown'], true);
                        $reportedFault = trim((string) ($serviceOrder->fault ?? ''));
                        $technicianReport = trim((string) ($serviceOrder->repair_steps ?? ''));
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 block">نوع دستگاه</label>
                            <div class="text-xl font-black text-slate-900 flex items-center gap-4 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                                    <i class="ti ti-device-laptop text-xl"></i>
                                </div>
                                {{ $deviceNotRegistered ? 'ثبت نشده' : $deviceType }}
                            </div>
                            <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-blue-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 block">مدل دستگاه</label>
                            <div class="text-xl font-black text-slate-900 flex items-center gap-4 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                                    <i class="ti ti-hash text-xl"></i>
                                </div>
                                {{ ($deviceNotRegistered || $deviceModel === '—') ? 'ثبت نشده' : $deviceModel }}
                            </div>
                            <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-indigo-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 block">شماره سریال</label>
                            <div class="text-lg font-bold text-slate-700 flex items-center gap-4 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-sm">
                                    <i class="ti ti-barcode text-xl"></i>
                                </div>
                                @if($serialNumber)
                                    <span class="dir-ltr font-mono">{{ $serialNumber }}</span>
                                @else
                                    <span class="text-slate-400 font-medium">ثبت نشده</span>
                                @endif
                            </div>
                            <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-amber-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        @if($assetNumber)
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 block">شماره اموال</label>
                            <div class="text-lg font-bold text-slate-700 flex items-center gap-4 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-colors shadow-sm">
                                    <i class="ti ti-id-badge text-xl"></i>
                                </div>
                                <span class="dir-ltr font-mono">{{ $assetNumber }}</span>
                            </div>
                            <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-violet-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        @endif

                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden relative">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 block">تاریخ پذیرش</label>
                            <div class="text-lg font-bold text-slate-700 flex items-center gap-4 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                                    <i class="ti ti-calendar-event text-xl"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span>
                                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                                            {{ \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d') }}
                                        @else
                                            {{ $serviceOrder->created_at->format('Y/m/d') }}
                                        @endif
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                                            {{ \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('H:i') }}
                                        @else
                                            {{ $serviceOrder->created_at->format('H:i') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-emerald-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                    </div>

                    <div class="border-t border-slate-50 pt-10 space-y-10">
                        <div class="animate-slide-up">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 block flex items-center gap-2">
                                <i class="ti ti-alert-circle text-amber-500 text-sm"></i>
                                مشکل اعلام شده توسط شما
                            </label>
                            <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-slate-100 text-slate-600 font-medium leading-relaxed relative overflow-hidden group hover:bg-white hover:shadow-inner transition-all duration-500">
                                <i class="ti ti-quote text-slate-200 text-7xl absolute -top-4 -right-4 opacity-50 group-hover:scale-110 transition-transform"></i>
                                <div class="relative z-10 whitespace-pre-wrap">{{ $reportedFault !== '' ? $reportedFault : 'ایرادی در زمان پذیرش ثبت نشده است.' }}</div>
                            </div>
                        </div>

                        @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::DELIVERED)
                        <div class="animate-slide-up">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 block">وضعیت گارانتی تعمیر</label>
                            @php
                                $deliveryDate = $serviceOrder->repair_completed_at ?? $serviceOrder->updated_at;
                                $warrantyDays = 90;
                                $expirationDate = $deliveryDate->copy()->addDays($warrantyDays);
                                $isExpired = now()->greaterThan($expirationDate);
                            @endphp
                            <div class="relative overflow-hidden p-8 rounded-[2.5rem] {{ $isExpired ? 'bg-rose-50 border-rose-100 text-rose-700' : 'bg-emerald-50 border-emerald-100 text-emerald-700' }} border group transition-all duration-500 hover:shadow-2xl">
                                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                                    <div class="flex items-center gap-8">
                                        <div class="w-20 h-20 {{ $isExpired ? 'bg-rose-100 text-rose-600 shadow-rose-200' : 'bg-emerald-100 text-emerald-600 shadow-emerald-200' }} rounded-[1.5rem] flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                            <i class="ti ti-{{ $isExpired ? 'shield-off' : 'shield-check' }} text-4xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-2xl mb-2">{{ $isExpired ? 'گارانتی منقضی شده است' : 'دستگاه دارای گارانتی فعال می‌باشد' }}</div>
                                            <p class="text-sm opacity-70 font-medium max-w-md">تمامی قطعات تعویض شده شامل گارانتی مجموعه پارس لیان می‌باشند.</p>
                                        </div>
                                    </div>
                                    <div class="px-8 py-4 bg-white/60 backdrop-blur-xl rounded-2xl text-base font-black border border-white/40 shadow-sm">
                                        <div class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">تاریخ انقضا</div>
                                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                                            {{ \Morilog\Jalali\Jalalian::fromCarbon($expirationDate)->format('Y/m/d') }}
                                        @else
                                            {{ $expirationDate->format('Y/m/d') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="absolute top-0 left-0 w-64 h-64 {{ $isExpired ? 'bg-rose-200/20' : 'bg-emerald-200/20' }} rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                            </div>
                        </div>
                        @endif

                        @if($technicianReport !== '')
                        <div class="animate-slide-up">
                            <label class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 block flex items-center gap-2">
                                <i class="ti ti-notes text-blue-500 text-sm"></i>
                                گزارش کارشناس فنی
                            </label>
                            <div class="bg-blue-50/40 p-8 rounded-[2.5rem] border border-blue-100 text-slate-700 font-medium leading-relaxed relative overflow-hidden group hover:bg-white hover:shadow-2xl transition-all duration-500">
                                <i class="ti ti-settings text-blue-100/60 text-9xl absolute -bottom-10 -left-10 group-hover:rotate-180 transition-transform duration-1000"></i>
                                <div class="relative z-10 whitespace-pre-wrap">{{ $technicianReport }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </x-enhanced-card>

                <!-- Repairs & Services -->
                @if($serviceOrder->repairItems->isNotEmpty())
                <x-enhanced-card title="قطعات و خدمات انجام شده" icon="tool" class="animate-slide-up">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-separate border-spacing-y-4">
                            <thead>
                                <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                    <th class="px-8 py-2">شرح خدمات / قطعه</th>
                                    <th class="px-8 py-2 text-center">هزینه (تومان)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviceOrder->repairItems as $item)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-500">
                                    <td class="px-8 py-5 first:rounded-r-[2rem] bg-white group-hover:bg-transparent transition-colors">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-12 transition-all duration-500 shadow-sm">
                                                <i class="ti ti-settings text-xl"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-700 group-hover:text-primary-600 transition-colors text-lg">{{ $item->name }}</span>
                                                @if($item->description)
                                                    <p class="text-xs text-slate-400 mt-1">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center bg-white group-hover:bg-transparent transition-colors last:rounded-l-[2rem]">
                                        <span class="font-black text-slate-900 bg-slate-100 px-5 py-2.5 rounded-xl group-hover:bg-white transition-colors text-lg shadow-sm border border-slate-200/50">{{ number_format($item->cost * $item->quantity) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white rounded-[2.5rem] overflow-hidden shadow-2xl relative">
                                    <td class="px-10 py-10 rounded-r-[2.5rem] font-black text-xl relative z-10">مجموع هزینه‌های نهایی</td>
                                    <td class="px-10 py-10 rounded-l-[2.5rem] text-center relative z-10">
                                        <div class="flex flex-col items-center">
                                            <div class="text-4xl font-black text-white mb-2 tracking-tight">
                                                {{ number_format($serviceOrder->total_cost) }}
                                            </div>
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">تومان</span>
                                        </div>
                                    </td>
                                    <!-- Decorative gradient overlay -->
                                    <div class="absolute inset-0 bg-white/5 opacity-50"></div>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </x-enhanced-card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/3 space-y-8">
                <!-- Timeline -->
                <x-enhanced-card title="تاریخچه تغییرات" icon="history" class="animate-slide-up">
                    <div class="relative space-y-10 pr-8 before:absolute before:right-0 before:top-2 before:bottom-2 before:w-1 before:bg-slate-100 before:rounded-full">
                        @forelse($serviceOrder->orderLogs as $log)
                        <div class="relative group">
                            <div class="absolute -right-[2.35rem] top-2 w-5 h-5 bg-white border-4 border-blue-600 rounded-full z-10 group-hover:scale-125 group-hover:bg-blue-600 transition-all duration-300 shadow-xl shadow-blue-500/20"></div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl tracking-wider group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                                            {{ \Morilog\Jalali\Jalalian::fromCarbon($log->created_at)->format('Y/m/d H:i') }}
                                        @else
                                            {{ $log->created_at->format('Y/m/d H:i') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="font-black text-base text-slate-900 group-hover:text-primary-600 transition-colors leading-tight">{{ $log->action_name }}</div>
                                <p class="text-sm text-slate-500 font-medium leading-relaxed bg-slate-50 p-5 rounded-[1.5rem] border border-slate-100 group-hover:bg-white group-hover:shadow-xl group-hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                                    <span class="relative z-10">{{ $log->customerDescription() }}</span>
                                    <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-blue-50 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="relative group">
                            <div class="absolute -right-[2.35rem] top-2 w-5 h-5 bg-white border-4 border-emerald-500 rounded-full z-10 shadow-xl shadow-emerald-500/20"></div>
                            <div class="space-y-3">
                                <div class="text-[10px] font-black text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl tracking-wider">
                                    @if(class_exists('\Morilog\Jalali\Jalalian'))
                                        {{ \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d H:i') }}
                                    @else
                                        {{ $serviceOrder->created_at->format('Y/m/d H:i') }}
                                    @endif
                                </div>
                                <div class="font-black text-base text-slate-900">ثبت اولیه سفارش</div>
                                <p class="text-sm text-slate-500 font-medium leading-relaxed bg-emerald-50/30 p-5 rounded-[1.5rem] border border-emerald-100/50">سفارش شما با موفقیت در سیستم ثبت شد.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </x-enhanced-card>

                <!-- Help Card -->
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

@push('scripts')
<script>
    function printOrder() {
        const originalTitle = document.title;
        document.title = "Service - {{ $serviceOrder->id }}";
        window.print();
        setTimeout(() => { document.title = originalTitle; }, 100);
    }
</script>
@endpush
