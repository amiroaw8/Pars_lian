@php
    $eventLabels = [
        'shop_online' => ['فروش آنلاین', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'shop_pos' => ['فروش حضوری', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'repair_use' => ['تعمیرات', 'bg-amber-50 text-amber-700 border-amber-200'],
        'repair_return' => ['برگشت تعمیر', 'bg-blue-50 text-blue-700 border-blue-200'],
        'stock_return' => ['برگشت موجودی', 'bg-blue-50 text-blue-700 border-blue-200'],
        'inventory_adjust' => ['تعدیل انبار', 'bg-rose-50 text-rose-700 border-rose-200'],
        'inventory_linked' => ['اتصال انبار', 'bg-violet-50 text-violet-700 border-violet-200'],
        'inventory_unlinked' => ['قطع انبار', 'bg-violet-50 text-violet-700 border-violet-200'],
        'product_edit' => ['ویرایش', 'bg-slate-50 text-slate-700 border-slate-200'],
        'out_of_stock' => ['اتمام موجودی', 'bg-rose-50 text-rose-700 border-rose-200'],
    ];
@endphp

@if($activities->isEmpty())
    <p class="text-sm text-slate-500 text-center py-4">هنوز رویدادی ثبت نشده است.</p>
@else
    <div class="space-y-3 max-h-80 overflow-y-auto">
        @foreach($activities as $activity)
            @php
                [$label, $badgeClass] = $eventLabels[$activity->event_type] ?? [$activity->event_type, 'bg-slate-50 text-slate-700 border-slate-200'];
            @endphp
            <div class="flex gap-3 p-3 rounded-xl bg-white border border-slate-100 text-sm">
                <div class="flex-shrink-0 pt-0.5">
                    <span class="inline-block px-2 py-1 rounded-lg text-[10px] font-black border {{ $badgeClass }}">{{ $label }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $activity->title }}</p>
                    @if($activity->description)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $activity->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3 mt-1 text-[10px] text-slate-400 font-bold">
                        <span>{{ $activity->occurred_at->format('Y/m/d H:i') }}</span>
                        @if($activity->quantity_change !== null)
                            <span class="{{ $activity->quantity_change < 0 ? 'text-rose-500' : 'text-emerald-600' }}">
                                {{ $activity->quantity_change > 0 ? '+' : '' }}{{ $activity->quantity_change }}
                            </span>
                        @endif
                        @if($activity->stock_after !== null)
                            <span>موجودی: {{ $activity->stock_after }}</span>
                        @endif
                        @if($activity->user_name)
                            <span>{{ $activity->user_name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
