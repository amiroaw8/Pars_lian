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

                <div>
                    <label for="new_category_parent_id" class="block text-xs font-bold text-slate-400 mb-2 uppercase">دسته والد (اختیاری)</label>
                    <select id="new_category_parent_id" class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">— سطح اول (بدون والد) —</option>
                        @foreach($categories as $id => $label)
                            <option value="{{ $id }}" title="{{ $categoryPaths[$id] ?? $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-2 font-bold">حداکثر سه سطح: اصلی → زیردسته → زیرزیردسته</p>
                </div>

                <div class="flex gap-3 pt-2">
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
