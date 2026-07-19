@php
    $selected = (array) request('category', []);
    $hasChildren = $category->children->isNotEmpty();
    $branchOpen = $hasChildren && $category->branchHasSelectedInFilter($selected);
@endphp
<div class="filter-item brand-tree-node" data-name="{{ $category->getFullPath() }}">
    @if($hasChildren)
        <button type="button"
                class="brand-tree-toggle w-full flex items-center gap-2 p-2 rounded-xl hover:bg-purple-50 transition-colors text-right"
                data-target="brand-children-{{ $category->id }}"
                aria-expanded="{{ $branchOpen ? 'true' : 'false' }}">
            <span class="text-sm font-bold text-gray-800 flex-1">{{ $category->name }}</span>
            <span class="text-[10px] font-bold text-purple-500 bg-purple-50 px-2 py-1 rounded-md">{{ $category->productsInTreeCount() }}</span>
            <i class="ti ti-chevron-down text-sm text-gray-400 transition-transform brand-tree-chevron flex-shrink-0 {{ $branchOpen ? 'rotate-180' : '' }}"></i>
        </button>
        <div id="brand-children-{{ $category->id }}" class="brand-tree-children space-y-1 mr-3 {{ $branchOpen ? '' : 'hidden' }}">
            @foreach($category->children as $child)
                <label class="flex items-center group cursor-pointer p-2 rounded-xl hover:bg-gray-50 transition-colors filter-item" data-name="{{ $child->getFullPath() }}">
                    <div class="relative flex items-center flex-shrink-0">
                        <input type="checkbox" name="category[]" value="{{ $child->id }}"
                               {{ \App\Models\ProductCategory::isSelectedInFilter($child, $selected) ? 'checked' : '' }}
                               class="peer appearance-none w-6 h-6 border-2 border-gray-200 rounded-lg checked:bg-purple-600 checked:border-purple-600 transition-all cursor-pointer"
                               onchange="this.form.submit()">
                        <i class="ti ti-check absolute inset-0 text-white text-xs opacity-0 peer-checked:opacity-100 flex items-center justify-center pointer-events-none"></i>
                    </div>
                    <span class="mr-3 text-gray-600 font-medium group-hover:text-purple-600 transition-colors text-sm flex-1">
                        {{ $child->name }}
                    </span>
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-md">{{ $child->productsInTreeCount() }}</span>
                </label>
            @endforeach
        </div>
    @endif
</div>
