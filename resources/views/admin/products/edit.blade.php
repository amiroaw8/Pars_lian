@extends('layouts.admin')

@section('title', 'ویرایش محصول - ' . $product->name)

@section('content')
<div class="p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-800">ویرایش محصول</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $product->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.history', $product) }}" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all">
                <i class="ti ti-history text-xl"></i>
                تاریخچه تغییرات
            </a>
            <a href="{{ route('admin.products.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all">
                <i class="ti ti-arrow-right text-xl"></i>
                بازگشت به لیست
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-100 text-rose-600 p-4 rounded-2xl mb-8">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Info -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ti ti-info-circle text-blue-600"></i>
                        اطلاعات اصلی محصول
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @include('admin.products.partials.inventory-link-card')

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">نام محصول (فارسی)</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">نام محصول (انگلیسی)</label>
                            <input type="text" name="name_en" value="{{ old('name_en', $product->name_en) }}" class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all text-left dir-ltr">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">دسته‌بندی</label>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.categories.index') }}" target="_blank" class="text-slate-500 hover:text-slate-700 text-[10px] font-black flex items-center gap-1 bg-slate-100 px-2 py-1 rounded-lg">
                                        <i class="ti ti-settings"></i> مدیریت
                                    </a>
                                    <button type="button" onclick="openCategoryModal()" class="text-blue-600 hover:text-blue-700 text-[10px] font-black flex items-center gap-1 bg-blue-50 px-2 py-1 rounded-lg">
                                        <i class="ti ti-plus"></i> ایجاد جدید
                                    </button>
                                </div>
                            </div>
                            <select name="category_id" id="category_id" required class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">انتخاب کنید...</option>
                                @foreach($categories as $id => $label)
                                    <option value="{{ $id }}" {{ old('category_id', $product->category_id) == $id ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">کد محصول (SKU)</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all text-left dir-ltr">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ti ti-align-right text-blue-600"></i>
                        توضیحات محصول
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">توضیح کوتاه</label>
                            <textarea name="short_description" rows="3" class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">توضیحات کامل</label>
                            <textarea name="description" rows="8" class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Technical Specs -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="ti ti-settings text-blue-600"></i>
                            مشخصات فنی
                        </span>
                        <button type="button" onclick="addSpecRow()" class="text-blue-600 hover:text-blue-700 font-bold text-sm flex items-center gap-1">
                            <i class="ti ti-plus"></i> افزودن ردیف
                        </button>
                    </h3>
                    
                    <div id="specs-container" class="space-y-4">
                        @php
                            $rawSpecs = $product->technical_specs ?? [];
                            $specKeys = [];
                            $specValues = [];
                            if (isset($rawSpecs['keys']) && is_array($rawSpecs['keys'])) {
                                $specKeys = $rawSpecs['keys'];
                                $specValues = $rawSpecs['values'] ?? [];
                            } elseif (is_array($rawSpecs)) {
                                foreach ($rawSpecs as $k => $v) {
                                    if (! is_numeric($k)) {
                                        $specKeys[] = $k;
                                        $specValues[] = $v;
                                    }
                                }
                            }
                        @endphp
                        @forelse($specKeys as $index => $key)
                        <div class="flex gap-4 spec-row">
                            <input type="text" name="technical_specs[keys][]" value="{{ $key }}" placeholder="ویژگی" class="flex-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                            <input type="text" name="technical_specs[values][]" value="{{ $specValues[$index] ?? '' }}" placeholder="مقدار" class="flex-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                            <button type="button" onclick="removeSpecRow(this)" class="bg-rose-50 text-rose-500 p-3 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                        @empty
                        <div class="flex gap-4 spec-row">
                            <input type="text" name="technical_specs[keys][]" placeholder="ویژگی" class="flex-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                            <input type="text" name="technical_specs[values][]" placeholder="مقدار" class="flex-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                            <button type="button" onclick="removeSpecRow(this)" class="bg-rose-50 text-rose-500 p-3 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Change Reason -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ti ti-message-dots text-blue-600"></i>
                        دلیل تغییرات
                    </h3>
                    <textarea name="change_reason" placeholder="مثلا: اصلاح قیمت، بروزرسانی موجودی..." class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all" rows="2"></textarea>
                    <p class="text-[10px] text-slate-400 mt-2">این متن در تاریخچه تغییرات محصول ذخیره می‌شود.</p>
                </div>

                <!-- Pricing & Inventory -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ti ti-coin text-blue-600"></i>
                        قیمت و موجودی
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <x-money-field
                                name="price"
                                label="قیمت اصلی (تومان)"
                                :value="old('price', $product->price ?? '')"
                                required
                                class="bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all font-black"
                            />
                        </div>
                        @php $hasDiscount = old('has_discount', $product->is_on_sale ?? false); @endphp
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="has_discount" value="1" id="has_discount" {{ $hasDiscount ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-blue-500" onchange="toggleSalePriceField()">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800 transition-colors">این محصول تخفیف‌دار است</span>
                        </label>
                        <div id="sale_price_wrapper" class="{{ $hasDiscount ? '' : 'hidden' }}">
                            <x-money-field
                                name="sale_price"
                                id="sale_price_input"
                                label="قیمت با تخفیف (تومان)"
                                :value="old('sale_price', $product->sale_price ?? 0)"
                                class="bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all font-black text-blue-600"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">موجودی فروشگاه</label>
                            @php $hasLinkedInventory = (bool) old('inventory_id', $product->inventory_id); @endphp
                            <input type="number" name="stock_quantity" id="stock_quantity_input" value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                                @if($hasLinkedInventory) readonly @endif
                                class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all {{ $hasLinkedInventory ? 'opacity-70 cursor-not-allowed' : '' }}">
                            @if($hasLinkedInventory)
                                <p class="text-xs text-blue-600 mt-2 font-bold">موجودی از «کالای انبار» خوانده می‌شود؛ تغییر موجودی فقط از پنل انبار یا فروش ثبت‌شده.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ti ti-toggle-right text-blue-600"></i>
                        وضعیت نمایش
                    </h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800 transition-colors">محصول فعال باشد</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-200 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800 transition-colors">نمایش در محصولات ویژه</span>
                        </label>
                    </div>
                </div>

                <!-- Images -->
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ti ti-photo text-blue-600"></i>
                        تصاویر محصول
                    </h3>
                    
                    <div class="space-y-4">
                        @if(!empty($product->all_image_urls))
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @foreach($product->all_image_urls as $img)
                            <div class="aspect-square rounded-xl bg-slate-50 border border-slate-100 overflow-hidden relative group">
                                <img loading="lazy" src="{{ $img }}" alt="" class="w-full h-full object-contain p-1">
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="relative border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-blue-400 transition-all group min-h-[120px]">
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImages(this)">
                            <i class="ti ti-cloud-upload text-4xl text-slate-300 group-hover:text-blue-500 transition-colors mb-2"></i>
                            <p class="text-xs font-bold text-slate-400">افزودن تصاویر بیشتر</p>
                        </div>
                        <div id="image-preview" class="grid grid-cols-3 gap-2"></div>
                    </div>
                </div>

                <!-- Save Button -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-5 rounded-3xl font-black text-lg transition-all shadow-xl shadow-blue-600/20 flex items-center justify-center gap-3">
                    <i class="ti ti-device-floppy text-2xl"></i>
                    بروزرسانی محصول
                </button>
            </div>
        </div>
    </form>
</div>

@push('modals')
<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeCategoryModal()"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 animate-zoom-in">
            <h3 class="text-xl font-black text-slate-800 mb-6">ایجاد دسته‌بندی جدید</h3>

            <div class="space-y-6">
                <div>
                    <label for="new_category_name" class="block text-xs font-bold text-slate-400 mb-2 uppercase">نام دسته‌بندی</label>
                    <input type="text" id="new_category_name" class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all" placeholder="مثلا: قطعات یدکی">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="saveQuickCategory()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black transition-all">
                        ذخیره دسته‌بندی
                    </button>
                    <button type="button" onclick="closeCategoryModal()" class="px-6 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold transition-all">
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection

@section('scripts')
<script>
    function toggleSalePriceField() {
        const checked = document.getElementById('has_discount')?.checked;
        const wrapper = document.getElementById('sale_price_wrapper');
        const input = document.getElementById('sale_price_input');
        if (wrapper) wrapper.classList.toggle('hidden', !checked);
        if (input) {
            input.required = !!checked;
            input.disabled = !checked;
        }
        if (checked && window.initMoneyInputs) {
            window.initMoneyInputs(wrapper || document);
        }
    }

    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('new_category_name').focus();
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('new_category_name').value = '';
    }

    async function saveQuickCategory() {
        const name = document.getElementById('new_category_name').value;
        if (!name) {
            alert('لطفا نام دسته‌بندی را وارد کنید.');
            return;
        }

        try {
            const response = await fetch("{{ route('admin.categories.store-quick') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name: name })
            });

            const data = await response.json();

            if (data.success) {
                // Add to select and select it
                const select = document.getElementById('category_id');
                const option = new Option(data.category.name, data.category.id, true, true);
                select.add(option);
                
                closeCategoryModal();
            } else {
                alert(data.message || 'خطایی رخ داده است.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('خطا در برقراری ارتباط با سرور.');
        }
    }

    function addSpecRow() {
        const container = document.getElementById('specs-container');
        const row = document.createElement('div');
        row.className = 'flex gap-4 spec-row animate-slide-up';
        row.innerHTML = `
            <input type="text" name="technical_specs[keys][]" placeholder="ویژگی" class="flex-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
            <input type="text" name="technical_specs[values][]" placeholder="مقدار" class="flex-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
            <button type="button" onclick="removeSpecRow(this)" class="bg-rose-50 text-rose-500 p-3 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                <i class="ti ti-trash"></i>
            </button>
        `;
        container.appendChild(row);
    }

    function removeSpecRow(btn) {
        const container = document.getElementById('specs-container');
        const rows = container.querySelectorAll('.spec-row');
        if (rows.length <= 1) {
            btn.closest('.spec-row').querySelectorAll('input').forEach(i => i.value = '');
            return;
        }
        btn.closest('.spec-row').remove();
    }

    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-xl bg-slate-50 border border-slate-100 overflow-hidden';
                    div.innerHTML = `<img src="${e.target.result}" alt="preview" class="w-full h-full object-contain p-1">`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', toggleSalePriceField);
</script>
@endsection
