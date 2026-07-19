@extends('layouts.admin')

@section('title', 'مدیریت دسته‌بندی محصولات - پارس لیان')

@section('content')
<div class="relative">
    <!-- Background Blobs -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <div class="flex flex-col gap-4 mb-10 relative z-10">
        <x-page-header 
            title="مدیریت دسته‌بندی محصولات" 
            subtitle="سازماندهی محصولات در گروه‌های مختلف برای دسترسی سریع‌تر و مدیریت بهتر."
            badge="Product Categories"
            badgeIcon="ti-category"
            headerIcon="ti-category"
            actionUrl="{{ route('admin.dashboard') }}"
            actionText="بازگشت به پنل"
            actionIcon="ti-layout-dashboard"
            class="w-full mb-0"
        />

        <div class="flex justify-start">
            <div class="bg-white/60 backdrop-blur-md px-6 py-4 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40">
                <form action="{{ route('admin.categories.index') }}" method="GET" id="filterForm">
                    <div class="flex items-center gap-4">
                        <label for="trashed" class="text-xs font-black text-slate-500 uppercase tracking-widest">نمایش:</label>
                        <select name="trashed" id="trashed" class="bg-transparent border-none focus:ring-0 font-black text-slate-800 cursor-pointer" onchange="this.form.submit()">
                            <option value="">فقط فعال</option>
                            <option value="1" {{ request('trashed') ? 'selected' : '' }}>سطل زباله</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-6 bg-emerald-50 border border-emerald-100 rounded-[2.5rem] flex items-center gap-5 text-emerald-600 animate-fade-in shadow-xl shadow-emerald-500/5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center shadow-inner">
                <i class="ti ti-circle-check text-2xl"></i>
            </div>
            <span class="font-black text-lg">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 p-6 bg-red-50 border border-red-100 rounded-[2.5rem] flex items-center gap-5 text-red-600 animate-fade-in shadow-xl shadow-red-500/5">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center shadow-inner">
                <i class="ti ti-alert-circle text-2xl"></i>
            </div>
            <span class="font-black text-lg">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start relative z-10">
        <!-- افزودن دسته‌بندی -->
        <div class="lg:sticky lg:top-8">
            <x-enhanced-card title="افزودن دسته‌بندی جدید" icon="plus" animated class="animate-fade-in shadow-2xl shadow-blue-500/5 border-white/50 backdrop-blur-sm">
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-8">
                    @csrf
                    <div class="form-group-modern group">
                        <label for="name" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-tag text-lg"></i>
                            نام دسته‌بندی
                        </label>
                        <input type="text" name="name" id="name" class="form-control-modern focus:ring-4 focus:ring-blue-500/10" placeholder="مثلاً قطعات یدکی، ابزارآلات" required>
                    </div>

                    <div class="form-group-modern group">
                        <label for="parent_id" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-hierarchy-2 text-lg"></i>
                            دسته والد (اختیاری)
                        </label>
                        <select name="parent_id" id="parent_id" class="form-control-modern focus:ring-4 focus:ring-blue-500/10">
                            <option value="">— سطح اول (بدون والد) —</option>
                            @foreach($parentOptions as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-2 font-bold">حداکثر سه سطح: اصلی → زیردسته → زیرزیردسته</p>
                    </div>

                    <div class="form-group-modern group">
                        <label for="description" class="form-label-modern group-focus-within:text-blue-600">
                            <i class="ti ti-align-left text-lg"></i>
                            توضیحات (اختیاری)
                        </label>
                        <textarea name="description" id="description" rows="3" class="form-control-modern focus:ring-4 focus:ring-blue-500/10" placeholder="توضیحات کوتاهی در مورد این دسته بنویسید..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-modern btn-modern-primary w-full py-5 justify-center text-lg shadow-2xl shadow-blue-500/20 hover:shadow-blue-500/40 transition-all active:scale-95 group">
                        <i class="ti ti-plus text-2xl group-hover:rotate-90 transition-transform duration-500"></i>
                        <span>ثبت دسته‌بندی</span>
                    </button>
                </form>
            </x-enhanced-card>
        </div>

        <!-- لیست دسته‌بندی‌ها -->
        <div class="lg:col-span-2">
            <x-enhanced-card title="ساختار درختی دسته‌بندی‌ها" icon="list" animated class="animate-fade-in shadow-2xl shadow-indigo-500/5 border-white/50 backdrop-blur-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400">برای مشاهده زیردسته‌ها روی فلش کنار هر دسته کلیک کنید.</p>
                    <div class="flex items-center gap-2">
                        <button type="button" id="expandAllCategoryTree" class="text-xs font-black text-blue-600 hover:text-blue-800 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                            باز کردن همه
                        </button>
                        <button type="button" id="collapseAllCategoryTree" class="text-xs font-black text-slate-500 hover:text-slate-700 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors">
                            بستن همه
                        </button>
                    </div>
                </div>
                <div class="space-y-4" id="categoryTree">
                    @forelse($categories as $category)
                        @include('admin.categories._node', [
                            'category' => $category,
                            'level' => 0,
                            'treeProductCounts' => $treeProductCounts,
                            'parentOptionPaths' => $parentOptionPaths,
                        ])
                    @empty
                        <div class="py-32 text-center">
                            <div class="flex flex-col items-center justify-center gap-8">
                                <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center shadow-inner animate-pulse">
                                    <i class="ti ti-category-2 text-7xl text-slate-200"></i>
                                </div>
                                <div class="space-y-2">
                                    <p class="font-black text-2xl text-slate-900 tracking-tight">هیچ دسته‌بندی یافت نشد</p>
                                    <p class="text-slate-400 font-medium">می‌توانید از پنل سمت راست اولین مورد را اضافه کنید.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </x-enhanced-card>
        </div>
    </div>
</div>

<!-- Modal ویرایش -->
<div id="editModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-all duration-500 opacity-0 [&.show]:flex [&.show]:opacity-100">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-500 scale-90 opacity-0 [.show_&]:scale-100 [.show_&]:opacity-100 border border-white/20">
        <div class="p-10">
            <div class="flex items-center justify-between mb-12">
                <h3 class="text-2xl font-black text-slate-900 flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 rounded-3xl flex items-center justify-center shadow-inner border border-blue-100/50">
                        <i class="ti ti-edit text-3xl"></i>
                    </div>
                    ویرایش دسته‌بندی
                </h3>
                <button type="button" class="w-12 h-12 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition-all" onclick="closeEditModal()">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-10">
                @csrf
                @method('PUT')
                <div class="form-group-modern group">
                    <label class="form-label-modern group-focus-within:text-blue-600">
                        <i class="ti ti-tag text-lg"></i>
                        نام دسته‌بندی
                    </label>
                    <input type="text" name="name" id="editName" class="form-control-modern text-lg font-black focus:ring-4 focus:ring-blue-500/10" required>
                </div>

                <div class="form-group-modern group">
                    <label class="form-label-modern group-focus-within:text-blue-600">
                        <i class="ti ti-align-left text-lg"></i>
                        توضیحات
                    </label>
                    <textarea name="description" id="editDescription" rows="3" class="form-control-modern focus:ring-4 focus:ring-blue-500/10"></textarea>
                </div>

                <div class="form-group-modern group">
                    <label class="form-label-modern group-focus-within:text-blue-600">
                        <i class="ti ti-hierarchy-2 text-lg"></i>
                        دسته والد
                    </label>
                    <select name="parent_id" id="editParentId" class="form-control-modern focus:ring-4 focus:ring-blue-500/10">
                        <option value="">— سطح اول —</option>
                        @foreach($parentOptions as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="btn-modern btn-modern-primary flex-1 py-5 justify-center text-lg shadow-2xl shadow-blue-500/20 active:scale-95 transition-all group">
                        <i class="ti ti-device-floppy text-2xl group-hover:scale-110 transition-transform"></i>
                        <span>ذخیره تغییرات</span>
                    </button>
                    <button type="button" class="btn-modern btn-modern-light px-10 py-5 text-slate-600 font-black hover:bg-slate-100 transition-all" onclick="closeEditModal()">انصراف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function setCategoryTreeExpanded(expanded) {
        document.querySelectorAll('#categoryTree [data-tree-children]').forEach(function(panel) {
            panel.classList.toggle('hidden', !expanded);
        });
        document.querySelectorAll('#categoryTree [data-tree-toggle]').forEach(function(btn) {
            btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            const icon = btn.querySelector('.tree-toggle-icon');
            if (icon) {
                icon.classList.toggle('-rotate-90', !expanded);
            }
        });
    }

    document.querySelectorAll('#categoryTree [data-tree-toggle]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const node = this.closest('.category-tree-node');
            const panel = node ? node.querySelector('[data-tree-children]') : null;
            if (!panel) return;
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            panel.classList.toggle('hidden', expanded);
            const icon = this.querySelector('.tree-toggle-icon');
            if (icon) {
                icon.classList.toggle('-rotate-90', expanded);
            }
        });
    });

    const expandAllBtn = document.getElementById('expandAllCategoryTree');
    const collapseAllBtn = document.getElementById('collapseAllCategoryTree');
    if (expandAllBtn) {
        expandAllBtn.addEventListener('click', function() { setCategoryTreeExpanded(true); });
    }
    if (collapseAllBtn) {
        collapseAllBtn.addEventListener('click', function() { setCategoryTreeExpanded(false); });
    }

    // Edit buttons logic
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            editCategory(
                this.dataset.id,
                this.dataset.name,
                this.dataset.description,
                this.dataset.parentId || ''
            );
        });
    });

    // Delete buttons logic
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const hasProducts = this.getAttribute('data-has-products') === 'true';
            const form = this.closest('form');
            
            if (hasProducts) {
                Swal.fire({
                    title: 'خطا!',
                    text: `دسته‌بندی "${itemName}" دارای محصول است و قابل حذف نیست. ابتدا محصولات آن را منتقل یا حذف کنید.`,
                    icon: 'error',
                    confirmButtonText: 'متوجه شدم',
                    customClass: {
                        container: 'font-vazir',
                        popup: 'rounded-[2rem]',
                        confirmButton: 'btn-modern btn-modern-primary px-6 py-2'
                    }
                });
                return;
            }

            Swal.fire({
                title: 'آیا اطمینان دارید؟',
                text: `دسته‌بندی "${itemName}" به سطل زباله منتقل خواهد شد.`,
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
                    form.submit();
                }
            });
        });
    });

    // Force delete buttons logic
    document.querySelectorAll('.btn-force-delete').forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'حذف دائمی؟',
                text: `دسته‌بندی "${itemName}" برای همیشه حذف خواهد شد و قابل بازیابی نیست!`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، برای همیشه حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    container: 'font-vazir',
                    popup: 'rounded-[2rem]',
                    confirmButton: 'btn-modern btn-modern-danger px-6 py-2',
                    cancelButton: 'btn-modern btn-modern-light px-6 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

function editCategory(id, name, description, parentId) {
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description ?? '';
    document.getElementById('editParentId').value = parentId || '';
    document.getElementById('editForm').action = '/panel/categories/' + id;
    document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('show');
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush
