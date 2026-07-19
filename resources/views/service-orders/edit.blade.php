@extends('layouts.admin')

@section('title', 'ویرایش سفارش سرویس - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-edit text-primary-400"></i>
                        ویرایش اطلاعات سفارش
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">سفارش سرویس <x-hash-ref :value="$serviceOrder->id" /></h2>
                    <p class="text-slate-300 text-lg font-medium max-w-xl leading-relaxed">بروزرسانی اطلاعات مشتری، دستگاه و جزئیات سرویس جهت ادامه فرآیند تعمیر.</p>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('automation.service-orders.show', $serviceOrder) }}" class="btn-modern btn-modern-light py-4 px-8 group">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به سفارش</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
        </div>

        <form method="POST" action="{{ route('automation.service-orders.update', $serviceOrder) }}" enctype="multipart/form-data" class="space-y-8 animate-slide-up" style="animation-delay: 0.1s;">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <!-- اطلاعات مشتری -->
                    <x-enhanced-card title="اطلاعات مشتری" icon="ti ti-users" animated>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end p-2">
                            <div class="form-group-modern group">
                                <label for="customer_id" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-user-check"></i>
                                    انتخاب مشتری
                                </label>
                                <div class="relative">
                                    <select name="customer_id" id="customerSelect" class="form-control-modern select2" required>
                                        <option value="">— انتخاب مشتری —</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $serviceOrder->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('customer_id')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="pb-1">
                                <a href="{{ route('automation.customers.create') }}" class="btn-modern btn-modern-primary w-full justify-center group py-4 rounded-2xl">
                                    <i class="ti ti-plus group-hover:rotate-90 transition-transform"></i>
                                    ایجاد مشتری جدید
                                </a>
                            </div>
                        </div>
                    </x-enhanced-card>

                    <!-- اطلاعات دستگاه -->
                    <x-enhanced-card title="اطلاعات دستگاه" icon="ti ti-device-laptop" animated>
                        @php
                            $allowDeviceChanges = $serviceOrder->status === 'registered';
                        @endphp

                        @if(!$allowDeviceChanges)
                            <div class="p-6 rounded-[2rem] bg-amber-50 border border-amber-100 mb-8 flex items-center gap-6 animate-fade-in">
                                <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0 shadow-inner">
                                    <i class="ti ti-lock text-amber-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-black text-amber-900">اطلاعات دستگاه قفل شده است</h4>
                                    <p class="text-xs text-amber-700 mt-1 leading-relaxed">به دلیل پیشرفت در مراحل سرویس، تغییر اطلاعات پایه دستگاه امکان‌پذیر نیست.</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-2">
                            <div class="form-group-modern group">
                                <label for="device_type" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-category"></i>
                                    نوع دستگاه
                                </label>
                                <div class="relative">
                                    <select name="device_type" id="deviceTypeSelect" class="form-control-modern pr-12 appearance-none" required {{ $allowDeviceChanges ? '' : 'disabled' }}>
                                        <option value="">— انتخاب نوع دستگاه —</option>
                                        @if($allowDeviceChanges)
                                            @foreach($deviceTypes as $type)
                                                @if(is_null($type->parent_id))
                                                    <option value="{{ $type->name }}" data-has-children="{{ $type->children->count() > 0 ? 'true' : 'false' }}" {{ $serviceOrder->device->type == $type->name ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="{{ $serviceOrder->device->type }}" selected>{{ $serviceOrder->device->type }}</option>
                                        @endif
                                    </select>
                                    @if(!$allowDeviceChanges)
                                        <input type="hidden" name="device_type" value="{{ $serviceOrder->device->type }}">
                                    @endif
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                    <i class="ti ti-layers-intersect absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('device_type')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="device_model" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-list"></i>
                                    مدل دستگاه
                                </label>
                                <div class="relative">
                                    <select name="device_model" id="deviceModelSelect" class="form-control-modern pr-12 appearance-none" required {{ $allowDeviceChanges ? '' : 'disabled' }}>
                                        @if($allowDeviceChanges)
                                            <option value="">— لطفاً منتظر بمانید —</option>
                                        @else
                                            <option value="{{ $serviceOrder->device->model }}" selected>{{ $serviceOrder->device->model }}</option>
                                        @endif
                                    </select>
                                    @if(!$allowDeviceChanges)
                                        <input type="hidden" name="device_model" value="{{ $serviceOrder->device->model }}">
                                    @endif
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                    <i class="ti ti-device-mobile absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('device_model')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="device_variant" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-tag"></i>
                                    جزئیات مدل (واریانت)
                                </label>
                                <div class="relative">
                                    <select name="device_variant" id="deviceVariantSelect" class="form-control-modern pr-12 appearance-none" {{ $allowDeviceChanges ? '' : 'disabled' }}>
                                        <option value="">— لطفاً منتظر بمانید —</option>
                                    </select>
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                    <i class="ti ti-id-badge absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>

                            <div class="form-group-modern group">
                                <label for="serial_number" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-barcode"></i>
                                    شماره سریال
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="serial_number"
                                        name="serial_number"
                                        class="form-control-modern pr-12 font-mono text-left dir-ltr"
                                        placeholder="S/N: 123456789"
                                        value="{{ old('serial_number', $serviceOrder->device->serial_number) }}"
                                        {{ $allowDeviceChanges ? '' : 'readonly' }}
                                    >
                                    <i class="ti ti-hash absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>

                            <div class="form-group-modern group">
                                <label for="asset_number" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-id-badge"></i>
                                    شماره اموال
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="asset_number"
                                        name="asset_number"
                                        class="form-control-modern pr-12 font-mono text-left dir-ltr"
                                        placeholder="Asset #: A-1001"
                                        value="{{ old('asset_number', $serviceOrder->device->asset_number) }}"
                                        {{ $allowDeviceChanges ? '' : 'readonly' }}
                                    >
                                    <i class="ti ti-tag absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>
                        </div>
                    </x-enhanced-card>

                    <!-- اطلاعات سرویس -->
                    <x-enhanced-card title="اطلاعات و مشخصات سرویس" icon="ti ti-settings" animated>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-2">
                            <div class="form-group-modern group">
                                <label for="service_type" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-building"></i>
                                    نوع سرویس درخواستی
                                </label>
                                <div class="relative">
                                    <select name="service_type" id="service_type" class="form-control-modern pr-12 appearance-none" required>
                                        <option value="in_company" {{ $serviceOrder->service_type == 'in_company' ? 'selected' : '' }}>🏛️ تعمیر در شرکت (In-House)</option>
                                        <option value="on_site" {{ $serviceOrder->service_type == 'on_site' ? 'selected' : '' }}>🏠 تعمیر در محل مشتری (On-Site)</option>
                                    </select>
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                    <i class="ti ti-tool absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('service_type')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="user_department" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-building-store"></i>
                                    واحد / دپارتمان بهره‌بردار
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="user_department"
                                        name="user_department"
                                        class="form-control-modern pr-12"
                                        placeholder="نام دپارتمان یا واحد سازمانی (اختیاری)"
                                        value="{{ old('user_department', $serviceOrder->user_department) }}"
                                    >
                                    <i class="ti ti-hierarchy absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>

                            <div class="form-group-modern group">
                                <label for="technician_id" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-tool"></i>
                                    تعمیرکار موردنظر
                                </label>
                                <div class="relative form-select-wrap">
                                    <select name="technician_id" id="technician_id" class="form-control-modern pr-12 bg-white h-11 w-full">
                                        <option value="">— بدون تعیین —</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}" {{ (string) old('technician_id', $serviceOrder->technician_id) === (string) $technician->id ? 'selected' : '' }}>
                                                {{ $technician->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-select-icon form-select-icon--boxed"><i class="ti ti-user-cog"></i></span>
                                </div>
                                @error('technician_id')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="receiver_name" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-user"></i>
                                    نام شخص تحویل‌دهنده
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="receiver_name"
                                        name="receiver_name"
                                        class="form-control-modern pr-12"
                                        placeholder="نام و نام خانوادگی رابط"
                                        value="{{ old('receiver_name', $serviceOrder->receiver_name) }}"
                                        required
                                    >
                                    <i class="ti ti-user-circle absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('receiver_name')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="receiver_phone" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-phone"></i>
                                    تلفن تماس تحویل‌دهنده
                                </label>
                                <div class="relative">
                                    <input
                                        type="tel"
                                        id="receiver_phone"
                                        name="receiver_phone"
                                        class="form-control-modern pr-12 ltr"
                                        placeholder="09123456789"
                                        value="{{ old('receiver_phone', $serviceOrder->receiver_phone) }}"
                                        required
                                    >
                                    <i class="ti ti-device-mobile absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('receiver_phone')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-8 mt-8 p-2">
                            <div class="form-group-modern group">
                                <label for="accessories" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-package"></i>
                                    لوازم همراه دستگاه
                                </label>
                                <div class="relative">
                                    <textarea
                                        id="accessories"
                                        name="accessories"
                                        class="form-control-modern pr-12 min-h-[100px]"
                                        placeholder="لیست قطعات و لوازم همراه (آداپتور، کابل، کیف و ...)"
                                    >{{ old('accessories', $serviceOrder->accessories) }}</textarea>
                                    <i class="ti ti-box absolute right-4 top-6 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>

                            <div class="form-group-modern group">
                                <label for="fault" class="form-label-modern group-focus-within:text-rose-600">
                                    <i class="ti ti-alert-triangle text-rose-500"></i>
                                    شرح کامل ایراد دستگاه
                                </label>
                                <div class="relative">
                                    <textarea
                                        id="fault"
                                        name="fault"
                                        class="form-control-modern pr-12 min-h-[120px] border-rose-100 focus:border-rose-500"
                                        placeholder="توضیحات دقیق در مورد مشکل یا خرابی دستگاه..."
                                        required
                                    >{{ old('fault', $serviceOrder->fault) }}</textarea>
                                    <i class="ti ti-message-report absolute right-4 top-6 text-rose-400 transition-colors group-focus-within:text-rose-500"></i>
                                </div>
                                @error('fault')
                                    <p class="mt-2 text-xs text-rose-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="notes" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-notes"></i>
                                    ملاحظات و یادداشت‌های پذیرش
                                </label>
                                <div class="relative">
                                    <textarea
                                        id="notes"
                                        name="notes"
                                        class="form-control-modern pr-12 min-h-[100px]"
                                        placeholder="هرگونه یادداشت یا نکته تکمیلی (اختیاری)"
                                    >{{ old('notes', $serviceOrder->notes) }}</textarea>
                                    <i class="ti ti-note absolute right-4 top-6 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>
                        </div>
                    </x-enhanced-card>
                </div>

                <div class="space-y-8">
                    <!-- فایل‌های ضمیمه -->
                    <x-enhanced-card title="مستندات ضمیمه" icon="ti ti-paperclip" animated>
                        <div class="space-y-8 p-2">
                            <div class="form-group-modern">
                                <label for="attachments" class="form-label-modern">انتخاب فایل جدید</label>
                                <div class="relative group">
                                    <input
                                        type="file"
                                        id="attachments"
                                        name="attachments[]"
                                        multiple
                                        accept="image/*,.pdf,.doc,.docx,.txt"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    >
                                    <div class="p-8 border-2 border-dashed border-slate-200 rounded-[2rem] group-hover:border-primary-300 group-hover:bg-primary-50/50 transition-all text-center">
                                        <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-100 group-hover:scale-110 transition-all duration-300">
                                            <i class="ti ti-upload text-3xl text-slate-400 group-hover:text-primary-600"></i>
                                        </div>
                                        <p class="text-sm font-black text-slate-700">کلیک کنید یا فایل‌ها را بکشید</p>
                                        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed">تصاویر، PDF، Word (حداکثر ۵ مگابایت)</p>
                                    </div>
                                </div>
                            </div>

                            <!-- پیش‌نمایش فایل‌های در حال آپلود -->
                            <div id="file-preview" class="hidden animate-fade-in">
                                <p class="text-xs font-black text-slate-700 mb-3 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span>
                                    فایل‌های جدید آماده آپلود:
                                </p>
                                <div id="preview-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                            </div>

                            @if($serviceOrder->attachments->count() > 0)
                                <div class="pt-8 border-t border-slate-100">
                                    <p class="text-[11px] font-black text-slate-400 mb-5 flex items-center gap-2 uppercase tracking-widest">
                                        <i class="ti ti-files text-sm"></i>
                                        فایل‌های قبلی ({{ $serviceOrder->attachments->count() }})
                                    </p>
                                    <div class="space-y-3">
                                        @foreach($serviceOrder->attachments as $attachment)
                                            <div class="flex items-center justify-between p-4 rounded-2xl border border-slate-100 bg-slate-50/30 hover:border-primary-200 hover:bg-white hover:shadow-xl hover:shadow-primary-500/5 transition-all duration-300 group">
                                                <div class="flex items-center gap-4 overflow-hidden">
                                                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                                                        @if(str_contains($attachment->mime_type, 'image/'))
                                                            <i class="ti ti-photo text-emerald-500 text-xl"></i>
                                                        @elseif(str_contains($attachment->mime_type, 'pdf'))
                                                            <i class="ti ti-file-text text-rose-500 text-xl"></i>
                                                        @else
                                                            <i class="ti ti-file text-blue-500 text-xl"></i>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-black text-slate-700 truncate max-w-[150px]">{{ $attachment->original_name }}</p>
                                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ number_format($attachment->size / 1024, 1) }} KB</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-primary-600 hover:border-primary-200 hover:shadow-md transition-all">
                                                        <i class="ti ti-eye text-lg"></i>
                                                    </a>
                                                    <button type="button" data-attachment-id="<?php echo $attachment->id; ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:shadow-md transition-all btn-delete-attachment">
                                                        <i class="ti ti-trash text-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-enhanced-card>

                    <!-- دکمه‌های عملیات -->
                    <x-enhanced-card title="تایید و بروزرسانی" icon="ti ti-bolt" animated>
                        <div class="space-y-4 p-2">
                            <button type="submit" class="btn-modern btn-modern-primary w-full justify-center py-5 shadow-xl shadow-primary-500/20 group text-lg rounded-2xl">
                                <i class="ti ti-check text-xl group-hover:scale-125 transition-transform"></i>
                                بروزرسانی اطلاعات سفارش
                            </button>
                            <a href="{{ route('automation.service-orders.show', $serviceOrder) }}" class="btn-modern btn-modern-secondary w-full justify-center py-4 rounded-2xl">
                                <i class="ti ti-x"></i>
                                انصراف و بازگشت
                            </a>
                        </div>
                    </x-enhanced-card>

                    <!-- Quick Tip -->
                    <div class="p-8 bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group animate-fade-in">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary-500/20 rounded-full blur-3xl group-hover:bg-primary-500/30 transition-all duration-700"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-6 border border-white/10 group-hover:scale-110 transition-transform duration-500">
                                <i class="ti ti-bulb text-primary-400 text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-black mb-3">نکته امنیتی</h4>
                            <p class="text-xs text-slate-400 leading-relaxed font-medium">پس از تایید نهایی دستگاه توسط کارشناس، برخی از اطلاعات پایه جهت حفظ یکپارچگی سیستم قفل خواهند شد.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($serviceOrder->attachments as $attachment)
    <form id="delete-attachment-{{ $attachment->id }}" action="{{ route('automation.service-orders.delete-attachment', [$serviceOrder, $attachment]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@endsection

@section('styles')
@include('partials.attachment-card-styles')
@endsection

@section('scripts')
<script src="{{ asset('js/attachment-preview.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deviceTypeSelect = document.getElementById('deviceTypeSelect');
    const deviceModelSelect = document.getElementById('deviceModelSelect');
    const deviceVariantSelect = document.getElementById('deviceVariantSelect');
    const attachmentsInput = document.getElementById('attachments');
    const filePreview = document.getElementById('file-preview');
    const previewContainer = document.getElementById('preview-container');
    
    const initialType = "{{ $serviceOrder->device->type }}";
    const initialModel = "{{ $serviceOrder->device->model }}";
    const initialVariant = "{{ $serviceOrder->device->variant }}";
    const allowDeviceChanges = <?php echo $allowDeviceChanges ? 'true' : 'false'; ?>;

    // Delete Attachment Logic
    document.querySelectorAll('.btn-delete-attachment').forEach(button => {
        button.addEventListener('click', function() {
            const attachmentId = this.getAttribute('data-attachment-id');
            if (confirm('آیا از حذف این فایل اطمینان دارید؟')) {
                const form = document.getElementById('delete-attachment-' + attachmentId);
                if (form) form.submit();
            }
        });
    });

    // Select2 Initialization with modern styling
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#customerSelect').select2({
            dir: "rtl",
            language: "fa",
            placeholder: "جستجوی نام یا تلفن مشتری...",
            allowClear: true
        });
    }

    if (allowDeviceChanges && initialType) {
        loadModels(initialType, initialModel);
    }

    if (deviceTypeSelect && allowDeviceChanges) {
        deviceTypeSelect.addEventListener('change', function() {
            loadModels(this.value);
        });
    }

    if (deviceModelSelect && allowDeviceChanges) {
        deviceModelSelect.addEventListener('change', function() {
            loadVariants(this.value);
        });
    }

    function loadModels(type, selectedModel = null) {
        if (!type) return;
        
        deviceModelSelect.innerHTML = '<option value="">— لطفاً منتظر بمانید —</option>';
        deviceModelSelect.disabled = true;
        deviceVariantSelect.innerHTML = '<option value="">— ابتدا مدل را انتخاب کنید —</option>';
        deviceVariantSelect.disabled = true;
        
        deviceModelSelect.parentElement.classList.add('animate-pulse');
        fetch("{{ url('api/device-types/children') }}/" + encodeURIComponent(type))
            .then(response => response.json())
            .then(data => {
                deviceModelSelect.innerHTML = '<option value="">— انتخاب مدل دستگاه —</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    if (selectedModel && item.name === selectedModel) {
                        option.selected = true;
                    }
                    deviceModelSelect.appendChild(option);
                });
                deviceModelSelect.disabled = false;
                deviceModelSelect.parentElement.classList.remove('animate-pulse');
                
                if (selectedModel) {
                    loadVariants(selectedModel, initialVariant);
                }
            });
    }

    function loadVariants(model, selectedVariant = null) {
        if (!model) return;
        
        deviceVariantSelect.innerHTML = '<option value="">— لطفاً منتظر بمانید —</option>';
        deviceVariantSelect.disabled = true;
        
        deviceVariantSelect.parentElement.classList.add('animate-pulse');
        fetch("{{ url('api/device-types/children') }}/" + encodeURIComponent(model))
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    deviceVariantSelect.innerHTML = '<option value="">— انتخاب جزئیات مدل —</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.name;
                        option.textContent = item.name;
                        if (selectedVariant && item.name === selectedVariant) {
                            option.selected = true;
                        }
                        deviceVariantSelect.appendChild(option);
                    });
                    deviceVariantSelect.disabled = false;
                } else {
                    deviceVariantSelect.innerHTML = '<option value="">جزئیاتی ثبت نشده است</option>';
                    deviceVariantSelect.disabled = true;
                }
                deviceVariantSelect.parentElement.classList.remove('animate-pulse');
            });
    }

    // File Preview Logic
    if (typeof initAttachmentFilePreview === 'function') {
        initAttachmentFilePreview(attachmentsInput, filePreview, previewContainer);
    }
});
</script>
@endsection
