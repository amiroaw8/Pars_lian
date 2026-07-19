@extends('layouts.admin')

@section('title', 'ثبت سفارش سرویس جدید - پارس لیان')

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
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-10 lg:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group transition-all duration-500">
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-8">
                <div class="text-center lg:text-right w-full lg:w-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-[10px] md:text-xs font-black mb-4 md:mb-6 border border-white/20 uppercase tracking-widest hover:bg-white/20 transition-colors cursor-default">
                        <i class="ti ti-clipboard-plus text-primary-400"></i>
                        پذیرش و ثبت سفارش
                    </div>
                    <h2 class="text-2xl md:text-4xl lg:text-5xl font-black text-white mb-3 md:mb-4 leading-tight">سفارش سرویس جدید</h2>
                    <p class="text-slate-300 text-sm md:text-lg font-medium max-w-xl leading-relaxed mx-auto lg:mx-0">ثبت دقیق اطلاعات مشتری، دستگاه و شرح ایراد جهت شروع فرآیند تخصصی تعمیر.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 w-full sm:w-auto">
                    <a href="{{ route('automation.service-orders.index') }}" class="btn-modern btn-modern-light py-3 md:py-4 px-6 md:px-8 group w-full sm:w-auto justify-center text-sm md:text-base">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به لیست</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
        </div>

        <form id="create-form" method="POST" action="{{ route('automation.service-orders.store') }}" enctype="multipart/form-data" class="space-y-8 animate-slide-up" style="animation-delay: 0.1s;">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <!-- اطلاعات مشتری -->
                    <x-enhanced-card title="اطلاعات مشتری" icon="ti ti-users" animated>
                        <div class="p-1">
                            <div class="bg-white/60 backdrop-blur-md rounded-2xl border border-white/50 p-6 transition-all hover:bg-white/80 hover:shadow-xl hover:shadow-primary-900/5 group/card relative">
                                <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary-500/5 rounded-full blur-2xl group-hover:bg-primary-500/10 transition-all"></div>
                                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>
                                <div class="relative z-10 flex flex-col lg:flex-row gap-4 lg:gap-6 items-start lg:items-end">
                                    <div class="flex-grow w-full lg:w-auto">
                                        <div class="form-group-modern group relative">
                                            <label for="customer_id" class="form-label-modern mb-2 group-focus-within:text-primary-600 flex items-center gap-2 text-sm md:text-base">
                                                <span class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 group-focus-within:text-primary-500 group-focus-within:shadow-primary-100 transition-all">
                                                    <i class="ti ti-user-check text-lg"></i>
                                                </span>
                                                <span class="font-bold text-slate-700">انتخاب مشتری</span>
                                            </label>
                                            <div class="relative">
                                                <select name="customer_id" id="customerSelect" class="form-control-modern select2 h-12 md:h-14 w-full" required>
                                                    <option value="">— جستجو و انتخاب مشتری —</option>
                                                    @if(isset($preselectedCustomer) && $preselectedCustomer)
                                                        <option value="{{ $preselectedCustomer->id }}" selected>{{ $preselectedCustomer->name }} — {{ $preselectedCustomer->phone }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            @error('customer_id')
                                                <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                                    <i class="ti ti-alert-circle"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="w-full lg:w-auto flex-shrink-0 pb-1">
                                        <a href="{{ route('automation.customers.create', ['return_to' => 'service_orders']) }}" class="btn-modern btn-modern-primary w-full lg:w-auto px-6 md:px-8 h-12 flex items-center justify-center gap-3 rounded-xl shadow-lg shadow-primary-500/20 hover:shadow-primary-500/40 hover:-translate-y-1 transition-all text-sm md:text-base">
                                            <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
                                                <i class="ti ti-plus text-xs"></i>
                                            </div>
                                            <span class="font-bold">مشتری جدید</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-enhanced-card>

                    <!-- اطلاعات دستگاه -->
                    <x-enhanced-card title="اطلاعات دستگاه" icon="ti ti-device-laptop" animated>
                        <x-slot:headerAction>
                            @if(auth()->user()->canManageInventory() || auth()->user()->isReceptionist())
                                <a href="{{ route('automation.device-types.index') }}" target="_blank" class="text-base bg-blue-50 text-blue-600 hover:text-blue-700 hover:bg-blue-100 px-6 py-3 rounded-2xl border border-blue-200 hover:border-blue-300 transition-all flex items-center gap-3 font-black shadow-md hover:shadow-lg group/btn">
                                    <i class="ti ti-settings text-xl group-hover/btn:rotate-45 transition-transform"></i>
                                    مدیریت و ویرایش انواع دستگاه‌ها
                                </a>
                            @endif
                        </x-slot:headerAction>
                        <div class="space-y-6 p-1">
                            <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/80 cursor-pointer hover:border-blue-300 hover:bg-blue-50/40 transition-all has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50/60">
                                <input type="checkbox" name="skip_device_registration" id="skipDeviceRegistration" value="1" class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500" {{ old('skip_device_registration') ? 'checked' : '' }}>
                                <span class="flex-1">
                                    <span class="block text-sm font-black text-slate-800">عدم ثبت مشخصات دستگاه</span>
                                    <span class="block text-xs text-slate-500 mt-0.5 font-medium">در صورت نبود اطلاعات دستگاه در زمان پذیرش، این گزینه را فعال کنید.</span>
                                </span>
                                <i class="ti ti-device-off text-2xl text-slate-400"></i>
                            </label>

                            <div id="deviceFieldsPanel">
                            <!-- Device Hierarchy Selection -->
                            <div class="bg-gradient-to-br from-blue-50/50 via-indigo-50/50 to-white rounded-2xl border border-blue-100/60 p-6 relative overflow-hidden group/hierarchy hover:border-blue-200 transition-all shadow-sm hover:shadow-md hover:shadow-blue-900/5">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none group-hover/hierarchy:bg-blue-500/10 transition-all duration-700"></div>
                                
                                <h4 class="text-xs font-black text-blue-800 uppercase tracking-wider mb-6 flex items-center gap-2">
                                    <i class="ti ti-sitemap"></i>
                                    مشخصات دستگاه
                                </h4>

                                <a href="{{ route('automation.device-types.index') }}" target="_blank" class="absolute top-4 left-4 text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-2xl border border-blue-100 shadow-sm hover:bg-blue-100 transition-all" title="مدیریت انواع دستگاه‌ها">
                                    <i class="ti ti-settings text-sm"></i>
                                    <span class="mr-2 font-black">انواع دستگاه</span>
                                </a>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 relative z-10">
                                    <!-- Type -->
                                    <div class="form-group-modern group">
                                        <label for="device_type" class="form-label-modern group-focus-within:text-primary-600 text-xs mb-1.5">نوع دستگاه</label>
                                        <div class="form-select-wrap">
                                            <select name="device_type" id="deviceTypeSelect" class="form-control-modern h-11 text-sm bg-white w-full" required>
                                                <option value="">انتخاب کنید...</option>
                                                @foreach($deviceTypes as $type)
                                                    @if(is_null($type->parent_id))
                                                        <option value="{{ $type->name }}" data-has-children="{{ $type->children->count() > 0 ? 'true' : 'false' }}">
                                                            {{ $type->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <i class="form-select-icon ti ti-category"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Model -->
                                    <div class="form-group-modern group">
                                        <label for="device_model" class="form-label-modern group-focus-within:text-primary-600 text-xs mb-1.5">مدل دستگاه</label>
                                        <div class="form-select-wrap">
                                            <select name="device_model" id="deviceModelSelect" class="form-control-modern h-11 text-sm bg-white w-full" required disabled>
                                                <option value="">ابتدا نوع را انتخاب کنید</option>
                                            </select>
                                            <i class="form-select-icon ti ti-list"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Variant -->
                                    <div class="form-group-modern group sm:col-span-2 lg:col-span-1">
                                        <label for="device_variant" class="form-label-modern group-focus-within:text-primary-600 text-xs mb-1.5">جزئیات / واریانت</label>
                                        <div class="form-select-wrap">
                                            <select name="device_variant" id="deviceVariantSelect" class="form-control-modern h-11 text-sm bg-white w-full" disabled>
                                                <option value="">ابتدا مدل را انتخاب کنید</option>
                                            </select>
                                            <i class="form-select-icon ti ti-tag"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Connector Lines (Desktop) - Removed for cleaner responsive design -->
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                            value="{{ old('serial_number') }}"
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
                                            value="{{ old('asset_number') }}"
                                        >
                                        <i class="ti ti-tag absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                    </div>
                                </div>
                            </div>
                            </div>{{-- /deviceFieldsPanel --}}
                        </div>
                    </x-enhanced-card>



                    <!-- اطلاعات سرویس -->
                    <x-enhanced-card title="اطلاعات و مشخصات سرویس" icon="ti ti-settings" animated>
                        <div class="space-y-8 p-1">
                            <!-- Operational Info -->
                            <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                                    <i class="ti ti-info-circle"></i>
                                    اطلاعات پذیرش
                                </h4>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                                    <div class="space-y-6">
                                        <!-- Service Type Radio Cards -->
                                        <div class="form-group-modern group">
                                            <label class="form-label-modern mb-3 text-xs font-bold text-slate-500 uppercase tracking-wider">نوع سرویس</label>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <!-- Option 1: In Company -->
                                                <label for="service_type_company" class="cursor-pointer relative group/option block h-full">
                                                    <input type="radio" id="service_type_company" name="service_type" value="in_company" class="peer sr-only" {{ old('service_type', 'in_company') == 'in_company' ? 'checked' : '' }}>
                                                    <div class="p-4 h-full rounded-2xl border-2 border-slate-100 bg-white hover:border-primary-200 hover:bg-primary-50/50 transition-all duration-300 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:shadow-lg peer-checked:shadow-primary-500/10 peer-checked:-translate-y-1 flex flex-row sm:flex-col items-center justify-between sm:justify-center gap-4 text-right sm:text-center">
                                                        <div class="flex items-center gap-4 sm:flex-col sm:gap-3 w-full sm:w-auto">
                                                            <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center peer-checked:bg-primary-500 peer-checked:text-white transition-all duration-300 group-hover/option:scale-110 group-hover/option:rotate-3 shadow-sm flex-shrink-0">
                                                                <i class="ti ti-building-warehouse text-xl"></i>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-sm font-black text-slate-700 peer-checked:text-primary-700 transition-colors">تعمیر در شرکت</span>
                                                                <span class="text-[10px] text-slate-400 font-medium mt-0.5 sm:hidden">دستگاه نزد ما می‌ماند</span>
                                                            </div>
                                                        </div>
                                                        <div class="w-6 h-6 rounded-full border-2 border-slate-300 group-has-[:checked]/option:border-primary-500 group-has-[:checked]/option:bg-primary-500 flex items-center justify-center transition-all sm:absolute sm:top-3 sm:left-3">
                                                            <i class="ti ti-check text-xs text-white opacity-0 group-has-[:checked]/option:opacity-100 transition-opacity"></i>
                                                        </div>
                                                    </div>
                                                </label>

                                                <!-- Option 2: On Site -->
                                                <label for="service_type_site" class="cursor-pointer relative group/option block h-full">
                                                    <input type="radio" id="service_type_site" name="service_type" value="on_site" class="peer sr-only" {{ old('service_type') == 'on_site' ? 'checked' : '' }}>
                                                    <div class="p-4 h-full rounded-2xl border-2 border-slate-100 bg-white hover:border-indigo-200 hover:bg-indigo-50/50 transition-all duration-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:shadow-lg peer-checked:shadow-indigo-500/10 peer-checked:-translate-y-1 flex flex-row sm:flex-col items-center justify-between sm:justify-center gap-4 text-right sm:text-center">
                                                        <div class="flex items-center gap-4 sm:flex-col sm:gap-3 w-full sm:w-auto">
                                                            <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center peer-checked:bg-indigo-500 peer-checked:text-white transition-all duration-300 group-hover/option:scale-110 group-hover/option:-rotate-3 shadow-sm flex-shrink-0">
                                                                <i class="ti ti-truck-delivery text-xl"></i>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-sm font-black text-slate-700 peer-checked:text-indigo-700 transition-colors">تعمیر در محل</span>
                                                                <span class="text-[10px] text-slate-400 font-medium mt-0.5 sm:hidden">تکنیسین اعزام می‌شود</span>
                                                            </div>
                                                        </div>
                                                        <div class="w-6 h-6 rounded-full border-2 border-slate-300 group-has-[:checked]/option:border-indigo-500 group-has-[:checked]/option:bg-indigo-500 flex items-center justify-center transition-all sm:absolute sm:top-3 sm:left-3">
                                                            <i class="ti ti-check text-xs text-white opacity-0 group-has-[:checked]/option:opacity-100 transition-opacity"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group-modern group">
                                            <label for="user_department" class="form-label-modern group-focus-within:text-primary-600 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                                واحد / دپارتمان
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="user_department" name="user_department" class="form-control-modern pr-12 bg-white h-11 focus:shadow-lg focus:shadow-primary-500/5 transition-all w-full" placeholder="مثال: حسابداری، انبار..." value="{{ old('user_department') }}">
                                                <div class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-focus-within:bg-primary-500 group-focus-within:text-white transition-all duration-300">
                                                    <i class="ti ti-sitemap"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group-modern group">
                                            <label for="technician_id" class="form-label-modern group-focus-within:text-primary-600 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                                <i class="ti ti-tool"></i>
                                                تعمیرکار موردنظر
                                            </label>
                                            <div class="relative form-select-wrap">
                                                <select name="technician_id" id="technician_id" class="form-control-modern pr-12 bg-white h-11 w-full" required>
                                                    <option value="">— انتخاب تعمیرکار —</option>
                                                    @foreach($technicians as $technician)
                                                        <option value="{{ $technician->id }}" {{ (string) old('technician_id') === (string) $technician->id ? 'selected' : '' }}>
                                                            {{ $technician->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="form-select-icon form-select-icon--boxed"><i class="ti ti-user-cog"></i></span>
                                            </div>
                                            @error('technician_id')
                                                <p class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1">
                                                    <i class="ti ti-alert-circle"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm relative group/receiver focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all mt-4 lg:mt-0">
                                        <div class="absolute -top-3 right-4 bg-white px-2 text-xs font-bold text-slate-500 group-focus-within/receiver:text-primary-600 transition-colors z-10">
                                            مشخصات تحویل دهنده
                                        </div>
                                        <div class="space-y-4 pt-2">
                                            <div class="relative">
                                                <input type="text" id="receiver_name" name="receiver_name" class="w-full border-none bg-transparent p-0 pl-8 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:ring-0" placeholder="نام و نام خانوادگی (اختیاری)" value="{{ old('receiver_name') }}">
                                                <i class="ti ti-user absolute left-0 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                            </div>
                                            <div class="w-full h-px bg-slate-100"></div>
                                            <div class="relative">
                                                <input type="tel" id="receiver_phone" name="receiver_phone" class="w-full border-none bg-transparent p-0 pl-8 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:ring-0 ltr text-right" placeholder="0912... (اختیاری)" value="{{ old('receiver_phone') }}">
                                                <i class="ti ti-phone absolute left-0 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Technical Info -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-6">
                                    <div class="form-group-modern group">
                                        <label for="accessories" class="form-label-modern group-focus-within:text-primary-600">
                                            <i class="ti ti-package"></i>
                                            لوازم همراه
                                        </label>
                                        <textarea id="accessories" name="accessories" class="form-control-modern min-h-[120px] resize-none" placeholder="آداپتور، کابل برق، کیف...">{{ old('accessories') }}</textarea>
                                    </div>
                                    
                                    <div class="form-group-modern group">
                                        <label for="notes" class="form-label-modern group-focus-within:text-primary-600">
                                            <i class="ti ti-note"></i>
                                            یادداشت پذیرش
                                        </label>
                                        <textarea id="notes" name="notes" class="form-control-modern min-h-[80px] resize-none" placeholder="توضیحات تکمیلی...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group-modern group h-full">
                                    <label for="fault" class="form-label-modern text-rose-600 flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="ti ti-alert-triangle text-rose-500"></i>
                                            شرح ایراد
                                        </span>
                                        <span class="text-[10px] bg-rose-100 text-rose-600 px-2 py-0.5 rounded-full">الزامی</span>
                                    </label>
                                    <div class="relative h-[calc(100%-2rem)]">
                                        <textarea id="fault" name="fault" class="form-control-modern w-full h-full min-h-[220px] resize-none border-rose-100 focus:border-rose-500 focus:ring-rose-500/20 bg-rose-50/10" placeholder="لطفاً ایراد دستگاه را به صورت دقیق و کامل شرح دهید..." required>{{ old('fault') }}</textarea>
                                        <i class="ti ti-message-exclamation absolute right-4 top-4 text-rose-300 opacity-50 text-6xl pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-enhanced-card>
                </div>

                <div class="space-y-8">
                    <!-- فایل‌های ضمیمه -->
                    <x-enhanced-card title="مستندات ضمیمه" icon="ti ti-paperclip" animated>
                        <div class="space-y-6 p-2">
                            <div class="form-group-modern">
                                <label for="attachments" class="form-label-modern">تصاویر یا مستندات (اختیاری)</label>
                                <div class="relative group">
                                    <input
                                        type="file"
                                        id="attachments"
                                        name="attachments[]"
                                        multiple
                                        accept="image/*,.pdf,.doc,.docx,.txt"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    >
                                    <div class="p-4 md:p-8 border-2 border-dashed border-slate-200 rounded-[2rem] group-hover:border-primary-300 group-hover:bg-primary-50/50 transition-all text-center">
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
                                    فایل‌های انتخاب شده:
                                </p>
                                <div id="preview-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                            </div>
                        </div>
                    </x-enhanced-card>

                    <!-- دکمه‌های عملیات -->
                    <x-enhanced-card title="تایید و ثبت نهایی" icon="ti ti-bolt" animated>
                        <div class="space-y-4 p-2">
                            <button type="submit" class="btn-modern btn-modern-primary w-full justify-center py-5 shadow-xl shadow-primary-500/20 group text-lg rounded-2xl">
                                <i class="ti ti-check text-xl group-hover:scale-125 transition-transform"></i>
                                ثبت سفارش سرویس
                            </button>
                            <a href="{{ route('automation.service-orders.index') }}" class="btn-modern btn-modern-secondary w-full justify-center py-4 rounded-2xl">
                                <i class="ti ti-x"></i>
                                انصراف و بازگشت
                            </a>
                        </div>
                        
                        <div class="mt-6 p-5 rounded-2xl bg-amber-50/50 border border-amber-100">
                            <div class="flex gap-3">
                                <i class="ti ti-info-circle text-amber-600 text-xl flex-shrink-0"></i>
                                <p class="text-[11px] text-amber-800 leading-relaxed font-medium">
                                    پس از ثبت سفارش، یک پیامک تایید حاوی شماره پیگیری برای مشتری ارسال خواهد شد.
                                </p>
                            </div>
                        </div>
                    </x-enhanced-card>

                    <!-- Quick Tip -->
                    <div class="p-8 bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group animate-fade-in">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary-500/20 rounded-full blur-3xl group-hover:bg-primary-500/30 transition-all duration-700"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-6 border border-white/10 group-hover:scale-110 transition-transform duration-500">
                                <i class="ti ti-bulb text-primary-400 text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-black mb-3">نکته کاربردی</h4>
                            <p class="text-xs text-slate-400 leading-relaxed font-medium">برای جستجوی سریع‌تر مشتری، می‌توانید از شماره تماس یا نام خانوادگی در کادر انتخاب استفاده کنید.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('partials.attachment-card-styles')
<style>
.select2-container { z-index: 9999 !important; }
.select2-dropdown { z-index: 10000 !important; }
</style>
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
    const createForm = document.getElementById('create-form');
    const skipDeviceCheckbox = document.getElementById('skipDeviceRegistration');
    const deviceFieldsPanel = document.getElementById('deviceFieldsPanel');

    function setDeviceFieldsSkipped(skipped) {
        if (!deviceFieldsPanel) return;
        deviceFieldsPanel.classList.toggle('opacity-50', skipped);
        deviceFieldsPanel.classList.toggle('pointer-events-none', skipped);
        [deviceTypeSelect, deviceModelSelect, deviceVariantSelect].forEach(function(el) {
            if (!el) return;
            if (skipped) {
                el.removeAttribute('required');
                el.disabled = true;
            } else {
                el.disabled = el.id !== 'deviceTypeSelect' && (el.id === 'deviceModelSelect' ? !deviceTypeSelect?.value : !deviceModelSelect?.value);
                if (el.id === 'deviceTypeSelect') {
                    el.setAttribute('required', 'required');
                    el.disabled = false;
                }
                if (el.id === 'deviceModelSelect' && deviceTypeSelect?.value) {
                    el.setAttribute('required', 'required');
                }
            }
        });
        ['serial_number', 'asset_number'].forEach(function(id) {
            const input = document.getElementById(id);
            if (input) input.disabled = skipped;
        });
    }

    if (skipDeviceCheckbox) {
        setDeviceFieldsSkipped(skipDeviceCheckbox.checked);
        skipDeviceCheckbox.addEventListener('change', function() {
            setDeviceFieldsSkipped(this.checked);
        });
    }

    // Select2 with AJAX search (name, phone, id)
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#customerSelect').select2({
            dir: "rtl",
            language: "fa",
            width: '100%',
            placeholder: "جستجوی نام، موبایل یا شناسه مشتری...",
            allowClear: true,
            minimumInputLength: 0,
            dropdownParent: $(document.body),
            ajax: {
                url: '{{ route('automation.customers.search') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    return { results: data.results || [] };
                },
                cache: true
            }
        });
        @if(isset($newCustomer) && $newCustomer)
        $.ajax({
            url: '{{ route('automation.customers.search') }}',
            data: { q: '{{ $newCustomer }}' }
        }).then(function(data) {
            const match = (data.results || []).find(r => String(r.id) === '{{ $newCustomer }}');
            if (match) {
                const option = new Option(match.text, match.id, true, true);
                $('#customerSelect').append(option).trigger('change');
            }
        });
        @endif
    }

    // Submit button loading state
    if (createForm) {
        createForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    در حال ثبت سفارش...
                `;
            }
        });
    }
    
    // Device Type Selection Logic
    if (deviceTypeSelect) {
        deviceTypeSelect.addEventListener('change', function() {
            const value = this.value;
            
            deviceModelSelect.innerHTML = '<option value="">— لطفاً منتظر بمانید —</option>';
            deviceModelSelect.disabled = true;
            deviceVariantSelect.innerHTML = '<option value="">— ابتدا مدل دستگاه را انتخاب کنید —</option>';
            deviceVariantSelect.disabled = true;
            
            if (value) {
                deviceModelSelect.parentElement.classList.add('animate-pulse');
                fetch("{{ url('api/device-types/children') }}/" + encodeURIComponent(value))
                    .then(response => response.json())
                    .then(data => {
                        deviceModelSelect.innerHTML = '<option value="">— انتخاب مدل دستگاه —</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.name;
                            option.textContent = item.name;
                            deviceModelSelect.appendChild(option);
                        });
                        deviceModelSelect.disabled = false;
                        deviceModelSelect.parentElement.classList.remove('animate-pulse');
                    })
                    .catch(error => {
                        console.error('Error fetching models:', error);
                        deviceModelSelect.innerHTML = '<option value="">خطا در بارگذاری اطلاعات</option>';
                    });
            }
        });
    }

    // Device Model Selection Logic
    if (deviceModelSelect) {
        deviceModelSelect.addEventListener('change', function() {
            const value = this.value;
            
            deviceVariantSelect.innerHTML = '<option value="">— لطفاً منتظر بمانید —</option>';
            deviceVariantSelect.disabled = true;
            
            if (value) {
                deviceVariantSelect.parentElement.classList.add('animate-pulse');
                fetch("{{ url('api/device-types/children') }}/" + encodeURIComponent(value))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            deviceVariantSelect.innerHTML = '<option value="">— انتخاب جزئیات مدل —</option>';
                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.name;
                                option.textContent = item.name;
                                deviceVariantSelect.appendChild(option);
                            });
                            deviceVariantSelect.disabled = false;
                        } else {
                            deviceVariantSelect.innerHTML = '<option value="">جزئیاتی ثبت نشده است</option>';
                            deviceVariantSelect.disabled = true;
                        }
                        deviceVariantSelect.parentElement.classList.remove('animate-pulse');
                    })
                    .catch(error => {
                        console.error('Error fetching variants:', error);
                        deviceVariantSelect.innerHTML = '<option value="">خطا در بارگذاری اطلاعات</option>';
                    });
            }
        });
    }

    // File Preview Logic
    if (typeof initAttachmentFilePreview === 'function') {
        initAttachmentFilePreview(attachmentsInput, filePreview, previewContainer);
    }
});
</script>
@endsection
