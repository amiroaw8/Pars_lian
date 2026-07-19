@php
    $hasChildren = $category->children->isNotEmpty();
@endphp
<div class="mobile-nav-category-node">
    @if($hasChildren)
        <button type="button"
                class="mobile-nav-tree-toggle w-full flex items-center p-4 rounded-2xl hover:bg-gray-50 text-gray-700 transition-colors text-right"
                data-target="mobile-cat-{{ $category->id }}"
                aria-expanded="false"
                style="padding-right: {{ 16 + (($depth ?? 0) * 12) }}px">
            <i class="ti ti-chevron-down text-gray-400 ml-3 transition-transform mobile-nav-chevron flex-shrink-0"></i>
            <span class="font-bold flex-1">{{ $category->name }}</span>
            <a href="{{ route('catalog.index', ['category' => [$category->slug]]) }}"
               class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg hover:bg-blue-100 flex-shrink-0"
               onclick="event.stopPropagation()">
                همه
            </a>
        </button>
        <div id="mobile-cat-{{ $category->id }}" class="mobile-nav-tree-children hidden mr-4 space-y-1 border-r-2 border-gray-100 pr-2">
            @foreach($category->children as $child)
                @include('layouts.partials.mobile-nav-category-node', ['category' => $child, 'depth' => ($depth ?? 0) + 1])
            @endforeach
        </div>
    @else
        <a href="{{ route('catalog.index', ['category' => [$category->slug]]) }}"
           class="flex items-center p-3 rounded-xl hover:bg-gray-50 text-gray-600 transition-colors text-sm font-medium"
           style="padding-right: {{ 12 + (($depth ?? 0) * 12) }}px">
            <i class="ti ti-chevron-left ml-2 text-gray-300 text-xs"></i>
            {{ $category->name }}
        </a>
    @endif
</div>
