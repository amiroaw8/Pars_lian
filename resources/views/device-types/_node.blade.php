@php
    $hasChildren = $type->children->isNotEmpty();
    $isRoot = $level === 0;
    $descendantCount = $type->countLoadedDescendants();
@endphp

<div class="device-type-node {{ $isRoot ? 'bg-white border border-slate-100 rounded-[2.5rem] p-6 md:p-8 shadow-sm hover:shadow-xl transition-all duration-300' : 'rounded-[1.25rem] p-4 bg-slate-50/70 border border-slate-100 hover:bg-white hover:border-blue-200 hover:shadow-md transition-all duration-300' }}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            @if($hasChildren)
                <button
                    type="button"
                    class="tree-toggle w-9 h-9 shrink-0 rounded-xl bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all"
                    aria-expanded="{{ $isRoot ? 'true' : 'false' }}"
                    data-tree-toggle
                    title="باز/بسته کردن زیرمجموعه"
                >
                    <i class="ti ti-chevron-down text-lg transition-transform duration-200 tree-toggle-icon {{ $isRoot ? '' : '-rotate-90' }}"></i>
                </button>
            @else
                <span class="w-9 h-9 shrink-0 flex items-center justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400 shadow-sm"></span>
                </span>
            @endif

            <div class="flex items-center gap-3 min-w-0">
                @if($isRoot)
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 flex items-center justify-center text-2xl shrink-0 border border-blue-100/50">
                        <i class="ti ti-category"></i>
                    </div>
                @endif
                <div class="min-w-0">
                    <h3 class="font-black text-slate-900 {{ $isRoot ? 'text-lg md:text-xl' : 'text-sm' }} tracking-tight truncate">{{ $type->name }}</h3>
                    <p class="text-slate-400 text-xs mt-0.5 font-bold">
                        سطح {{ $level + 1 }} • شناسه: <x-hash-ref :value="$type->id" />
                        @if($descendantCount > 0)
                            • {{ $descendantCount }} زیرمجموعه
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 mr-12 sm:mr-0">
            @if($type->trashed())
                <form method="POST" action="{{ route('automation.device-types.restore', $type->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-sm border border-emerald-100" title="بازیابی">
                        <i class="ti ti-refresh text-lg"></i>
                    </button>
                </form>
                <form method="POST" action="{{ route('automation.device-types.force-delete', $type->id) }}" class="inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm border border-red-100 btn-force-delete"
                            data-item-name="{{ $type->name }}">
                        <i class="ti ti-trash-x text-lg"></i>
                    </button>
                </form>
            @else
                <button type="button" class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-blue-500 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm edit-btn"
                        data-id="{{ $type->id }}"
                        data-name="{{ $type->name }}"
                        data-parent-id="{{ $type->parent_id }}"
                        title="ویرایش">
                    <i class="ti ti-edit text-lg"></i>
                </button>
                <form method="POST" action="{{ route('automation.device-types.destroy', $type) }}" class="inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-red-500 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm btn-delete"
                            data-item-name="{{ $type->name }}"
                            data-has-children="{{ $hasChildren ? 'true' : 'false' }}"
                            title="حذف">
                        <i class="ti ti-trash text-lg"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($hasChildren)
        <div
            class="tree-children mt-4 mr-4 pr-4 border-r-2 border-slate-100 space-y-2 {{ $isRoot ? '' : 'hidden' }}"
            data-tree-children
        >
            @foreach($type->children as $child)
                @include('device-types._node', ['type' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
