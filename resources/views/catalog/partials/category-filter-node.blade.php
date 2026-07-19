@php
    $rawSelected = request('category');
    $selectedCategory = is_array($rawSelected) ? ($rawSelected[0] ?? null) : $rawSelected;
    $selectedForBranch = $selectedCategory !== null && $selectedCategory !== '' ? [(string) $selectedCategory] : [];
    $searchPath = $category->getFullPath();
    $hasChildren = $category->children->isNotEmpty();
    $isSelected = $selectedCategory !== null && $selectedCategory !== '' && (string) $category->id === (string) $selectedCategory;
    $branchOpen = $hasChildren && $category->branchHasSelectedInFilter($selectedForBranch);
    $radioId = 'catalog-cat-' . $category->id;
@endphp
<div class="filter-item category-tree-node" data-name="{{ $searchPath }}">
    <div class="flex items-center gap-1" style="padding-right: {{ $depth * 12 }}px">
        @if($hasChildren)
            <button type="button"
                    class="category-tree-toggle flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    data-target="cat-children-{{ $category->id }}"
                    aria-label="باز و بسته کردن زیردسته‌های {{ $category->name }}"
                    aria-expanded="{{ $branchOpen ? 'true' : 'false' }}">
                <i class="ti ti-chevron-down text-sm text-gray-400 transition-transform category-tree-chevron {{ $branchOpen ? 'rotate-180' : '' }}"></i>
            </button>
        @else
            <span class="w-8 flex-shrink-0" aria-hidden="true"></span>
        @endif

        <label for="{{ $radioId }}"
               class="catalog-category-option flex-1 min-w-0 {{ $isSelected ? 'is-selected' : '' }}">
            <input type="radio"
                   id="{{ $radioId }}"
                   name="category"
                   value="{{ $category->id }}"
                   {{ $isSelected ? 'checked' : '' }}
                   class="sr-only"
                   onchange="this.form.submit()">
            <span class="catalog-category-option__icon">
                <i class="ti ti-{{ $hasChildren ? 'folder' : 'tag' }}"></i>
            </span>
            <span class="catalog-category-option__text truncate">{{ $category->name }}</span>
            <span class="text-[10px] font-bold text-gray-400 bg-white/80 px-2 py-0.5 rounded-md flex-shrink-0 {{ $isSelected ? 'text-blue-500' : '' }}">{{ $category->productsInTreeCount() }}</span>
        </label>
    </div>
    @if($hasChildren)
        <div id="cat-children-{{ $category->id }}" class="category-tree-children space-y-1 mr-2 mt-1 {{ $branchOpen ? '' : 'hidden' }}">
            @foreach($category->children as $child)
                @include('catalog.partials.category-filter-node', ['category' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
