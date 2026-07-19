@extends('layouts.admin')

@section('title', 'ویرایش دستگاه - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-slate-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-edit text-amber-400"></i>
                        ویرایش اطلاعات فنی
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">ویرایش: {{ $device->model }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                        <span class="px-4 py-1.5 rounded-xl bg-white/10 backdrop-blur-md text-white border border-white/20 text-xs font-black uppercase tracking-widest">
                            {{ $device->type }}
                        </span>
                        <span class="px-4 py-1.5 rounded-xl bg-amber-500/20 backdrop-blur-md text-amber-300 border border-amber-500/30 text-xs font-black">
                            ID: <x-hash-ref :value="$device->id" />
                        </span>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('automation.devices.show', $device) }}" class="btn-modern btn-modern-light py-4 px-8 group">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به جزئیات</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-amber-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-amber-500/20 transition-colors duration-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-8">
                <x-enhanced-card variant="default" title="مشخصات فنی دستگاه" icon="ti-settings" animated>
                    <form action="{{ route('automation.devices.update', $device) }}" method="POST" class="space-y-8 p-2">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Customer Select -->
                            <div class="form-group-modern group">
                                <label for="customer_id" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-user-check"></i>
                                    مالک دستگاه (مشتری)
                                </label>
                                <div class="relative">
                                    <select name="customer_id" id="customer_id" class="form-control-modern pr-12 @error('customer_id') border-danger-500 @enderror select2" required>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ (old('customer_id') ?? $device->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform group-focus-within:rotate-180"></i>
                                    <i class="ti ti-user absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('customer_id')
                                    <p class="text-danger-600 text-xs mt-2 flex items-center gap-1">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div class="form-group-modern group">
                                <label for="type" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-category"></i>
                                    نوع دستگاه
                                </label>
                                <div class="relative">
                                    <input type="text" name="type" id="type" class="form-control-modern pr-12 @error('type') border-danger-500 @enderror" value="{{ old('type') ?? $device->type }}" required placeholder="مثال: لپ‌تاپ، تبلت...">
                                    <i class="ti ti-device-laptop absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('type')
                                    <p class="text-danger-600 text-xs mt-2 flex items-center gap-1">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Model -->
                            <div class="form-group-modern group">
                                <label for="model" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-device-desktop"></i>
                                    مدل دقیق
                                </label>
                                <div class="relative">
                                    <input type="text" name="model" id="model" class="form-control-modern pr-12 @error('model') border-danger-500 @enderror" value="{{ old('model') ?? $device->model }}" required placeholder="مثال: MacBook Pro M2 2023">
                                    <i class="ti ti-tag absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('model')
                                    <p class="text-danger-600 text-xs mt-2 flex items-center gap-1">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Asset Number -->
                            <div class="form-group-modern group">
                                <label for="asset_number" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-hash"></i>
                                    شماره اموال / سریال
                                </label>
                                <div class="relative">
                                    <input type="text" name="asset_number" id="asset_number" class="form-control-modern pr-12 @error('asset_number') border-danger-500 @enderror" value="{{ old('asset_number') ?? $device->asset_number }}" placeholder="شماره شناسایی داخلی یا سریال سازنده">
                                    <i class="ti ti-barcode absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('asset_number')
                                    <p class="text-danger-600 text-xs mt-2 flex items-center gap-1">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Guarantee -->
                            <div class="md:col-span-2">
                                <label class="flex items-center gap-4 cursor-pointer group/check p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-primary-300 hover:bg-primary-50/30 transition-all duration-300">
                                    <div class="relative flex items-center">
                                        <input type="hidden" name="has_guarantee" value="0">
                                        <input type="checkbox" name="has_guarantee" id="has_guarantee" value="1" {{ (old('has_guarantee') ?? $device->has_guarantee) ? 'checked' : '' }} class="w-6 h-6 rounded-lg border-slate-300 text-primary-600 focus:ring-primary-500 transition-all cursor-pointer">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-700 group-hover/check:text-primary-700 transition-colors">این دستگاه دارای گارانتی فعال است</span>
                                        <span class="text-xs text-slate-500">در صورت انتخاب، هزینه‌های قطعات طبق ضوابط گارانتی محاسبه می‌شود.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-8 border-t border-slate-100">
                            <button type="submit" class="btn-modern btn-modern-primary py-4 px-10 w-full sm:w-auto shadow-xl shadow-primary-500/20 group">
                                <i class="ti ti-device-floppy group-hover:scale-110 transition-transform"></i>
                                <span>ذخیره تغییرات</span>
                            </button>
                            <a href="{{ route('automation.devices.show', $device) }}" class="btn-modern btn-modern-light py-4 px-10 w-full sm:w-auto">
                                <i class="ti ti-x"></i>
                                <span>انصراف</span>
                            </a>
                        </div>
                    </form>
                </x-enhanced-card>
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Help Card -->
                <x-enhanced-card variant="warning" title="راهنمای ویرایش" icon="ti-help" animated>
                    <div class="space-y-4">
                        <div class="flex gap-4 p-3 rounded-2xl bg-white/50 border border-white/80 hover:border-amber-200 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                                <i class="ti ti-user-edit"></i>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-black text-slate-800">تغییر مالک</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">اگر دستگاه به مشتری دیگری فروخته شده، می‌توانید مالک آن را در اینجا تغییر دهید.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 p-3 rounded-2xl bg-white/50 border border-white/80 hover:border-amber-200 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="ti ti-history"></i>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-black text-slate-800">سوابق فنی</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">تغییر مدل یا نوع دستگاه، روی سوابق قبلی این دستگاه در سیستم تاثیر می‌گذارد.</p>
                            </div>
                        </div>
                    </div>
                </x-enhanced-card>

                <!-- Danger Zone -->
                <x-enhanced-card variant="danger" title="عملیات حساس" icon="ti-alert-triangle" animated>
                    <div class="p-4 rounded-2xl bg-danger-50 border border-danger-100 mb-6">
                        <h4 class="text-sm font-black text-danger-800 mb-2 flex items-center gap-2">
                            <i class="ti ti-trash"></i>
                            حذف دائمی دستگاه
                        </h4>
                        <p class="text-xs text-danger-700 leading-relaxed">
                            با حذف دستگاه، تمامی سوابق تعمیراتی و اطلاعات فنی مرتبط با آن به صورت دائمی از سیستم حذف خواهد شد. این عملیات قابل بازگشت نیست.
                        </p>
                    </div>
                    
                    <form action="{{ route('automation.devices.destroy', $device) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-modern btn-modern-danger w-full justify-center py-4 group delete-device-btn">
                            <i class="ti ti-trash group-hover:rotate-12 transition-transform"></i>
                            <span>حذف از بانک اطلاعاتی</span>
                        </button>
                    </form>
                </x-enhanced-card>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle Delete Confirmation
    document.querySelectorAll('.delete-device-btn').forEach(button => {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'آیا اطمینان دارید؟',
                text: "تمامی سوابق این دستگاه به صورت دائمی حذف خواهند شد و این عمل قابل بازگشت نیست!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    container: 'font-vazir',
                    popup: 'rounded-[2rem]',
                    confirmButton: 'btn-modern btn-modern-danger px-6 py-2',
                    cancelButton: 'btn-modern btn-modern-light px-6 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });

    // Add loading state to form submission
    document.querySelector('form[action*="update"]').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `
            <i class="ti ti-loader animate-spin"></i>
            <span>در حال ذخیره‌سازی...</span>
        `;
    });
</script>

<style>
    .font-vazir { font-family: 'Vazirmatn', sans-serif !important; }
</style>
@endsection
