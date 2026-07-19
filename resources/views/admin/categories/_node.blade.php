@php
    $hasChildren = $category->children->isNotEmpty();
    $isRoot = $level === 0;
    $directCount = (int) ($category->products_count ?? 0);
    $treeCount = (int) (($treeProductCounts[$category->id] ?? null) ?? $directCount);
    $childCount = $category->children->count();
@endphp

<div class="category-tree-node {{ $isRoot ? 'bg-white border border-slate-100 rounded-[2rem] p-6 shadow-sm hover:shadow-xl transition-all duration-300' : 'rounded-[1.25rem] p-4 bg-slate-50/70 border border-slate-100 hover:bg-white hover:border-blue-200 hover:shadow-md transition-all duration-300' }}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            @if($hasChildren)
                <button
                    type="button"
                    class="tree-toggle w-9 h-9 shrink-0 rounded-xl bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all"
                    aria-expanded="{{ $isRoot ? 'true' : 'false' }}"
                    data-tree-toggle
                    title="باز/بسته کردن زیردسته"
                >
                    <i class="ti ti-chevron-down text-lg transition-transform duration-200 tree-toggle-icon {{ $isRoot ? '' : '-rotate-90' }}"></i>
                </button>
            @else
                <span class="w-9 h-9 shrink-0 flex items-center justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400 shadow-sm"></span>
                </span>
            @endif

            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <i class="ti ti-{{ $level === 0 ? 'category' : ($level === 1 ? 'category-2' : 'subtask') }}"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-black text-slate-900 {{ $isRoot ? 'text-lg' : 'text-sm' }} tracking-tight truncate">{{ $category->name }}</h3>
                    <p class="text-slate-400 text-xs mt-0.5 font-bold">
                        سطح {{ $level + 1 }}
                        • <x-hash-ref :value="$category->id" />
                        • {{ $treeCount }} محصول
                        @if($directCount !== $treeCount)
                            <span class="text-slate-500">({{ $directCount }} مستقیم)</span>
                        @endif
                        @if($childCount > 0)
                            • {{ $childCount }} زیردسته
                        @endif
                    </p>
                    @if(!empty($parentOptionPaths[$category->id] ?? null) && $level > 0)
                        <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $parentOptionPaths[$category->id] }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 mr-12 sm:mr-0">
            @if($category->trashed())
                <form method="POST" action="{{ route('admin.categories.restore', $category->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all" title="بازیابی">
                        <i class="ti ti-refresh"></i>
                    </button>
                </form>
            @else
                <button type="button" class="w-10 h-10 rounded-xl bg-slate-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all edit-btn"
                    data-id="{{ $category->id }}"
                    data-name="{{ $category->name }}"
                    data-description="{{ $category->description }}"
                    data-parent-id="{{ $category->parent_id }}">
                    <i class="ti ti-edit"></i>
                </button>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        class="w-10 h-10 rounded-xl bg-slate-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all btn-delete"
                        data-item-name="{{ $category->name }}"
                        data-has-products="{{ ($treeCount > 0 || $hasChildren) ? 'true' : 'false' }}">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($category->description)
        <p class="mt-3 text-sm text-slate-500 border-t border-slate-100 pt-3">{{ $category->description }}</p>
    @endif

    @if($hasChildren)
        <div
            class="tree-children mt-4 mr-4 pr-4 border-r-2 border-slate-100 space-y-2 {{ $isRoot ? '' : 'hidden' }}"
            data-tree-children
        >
            @foreach($category->children as $child)
                @include('admin.categories._node', [
                    'category' => $child,
                    'level' => $level + 1,
                    'treeProductCounts' => $treeProductCounts,
                    'parentOptionPaths' => $parentOptionPaths,
                ])
            @endforeach
        </div>
    @endif
</div>
