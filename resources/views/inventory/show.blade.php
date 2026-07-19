@extends('layouts.admin')

@section('title', 'مشاهده کالا - ' . $inventory->name)

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-slate-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-package text-primary-400"></i>
                        جزئیات کالا
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">{{ $inventory->name }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                        <span class="px-4 py-1.5 rounded-xl bg-white/10 backdrop-blur-md text-white border border-white/20 text-xs font-black uppercase tracking-widest">
                            ID: <x-hash-ref :value="$inventory->id" />
                        </span>
                        @if($inventory->type == 'device')
                            <span class="px-4 py-1.5 rounded-xl bg-blue-500/20 backdrop-blur-md text-blue-300 border border-blue-500/30 text-xs font-black">دستگاه</span>
                        @elseif($inventory->type == 'part')
                            <span class="px-4 py-1.5 rounded-xl bg-amber-500/20 backdrop-blur-md text-amber-300 border border-amber-500/30 text-xs font-black">قطعه</span>
                        @elseif($inventory->type == 'tool')
                            <span class="px-4 py-1.5 rounded-xl bg-emerald-500/20 backdrop-blur-md text-emerald-300 border border-emerald-500/30 text-xs font-black">ابزار</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('automation.inventory.edit', $inventory) }}" class="btn-modern btn-modern-warning py-4 px-8 shadow-xl shadow-amber-500/20 group">
                        <i class="ti ti-edit group-hover:rotate-12 transition-transform"></i>
                        <span>ویرایش اطلاعات</span>
                    </a>
                    <a href="{{ route('automation.inventory.index') }}" class="btn-modern btn-modern-light py-4 px-8 group">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به لیست</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
        </div>

        @if(isset($stockMismatches) && $stockMismatches->isNotEmpty())
        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fade-in">
            <div>
                <p class="font-black text-amber-900">ناسازگاری موجودی فروشگاه و انبار</p>
                <p class="text-sm text-amber-800 mt-1">{{ $stockMismatches->count() }} محصول فروشگاه با موجودی انبار ({{ $inventory->quantity }}) هم‌خوان نیست.</p>
            </div>
            <form method="POST" action="{{ route('automation.inventory.sync-shop-products', $inventory) }}">
                @csrf
                <button type="submit" class="btn-modern btn-modern-warning">همگام‌سازی از انبار</button>
            </form>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Info -->
            <div class="space-y-8">
                @if(isset($linkedProducts) && $linkedProducts->isNotEmpty())
                <x-enhanced-card title="محصولات فروشگاه متصل" icon="ti-shopping-cart" animated>
                    <div class="space-y-3">
                        @foreach($linkedProducts as $linked)
                        <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100 text-sm">
                            <a href="{{ route('admin.products.edit', $linked->id) }}" class="font-bold text-slate-800 hover:text-primary-600">{{ $linked->name }}</a>
                            <span class="font-black {{ (int)$linked->stock_quantity === (int)$inventory->quantity ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $linked->stock_quantity }} / {{ $inventory->quantity }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </x-enhanced-card>
                @endif

                <x-enhanced-card title="مشخصات فنی" icon="ti-info-circle" animated>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">موجودی فعلی</span>
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-black {{ $inventory->quantity <= $inventory->min_quantity ? 'text-danger-600' : 'text-emerald-600' }}">
                                    {{ $inventory->quantity }}
                                </span>
                                @if($inventory->quantity <= $inventory->min_quantity)
                                    <span class="animate-pulse w-2 h-2 rounded-full bg-danger-500 shadow-lg shadow-danger-500/50"></span>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">قیمت واحد</span>
                            <div class="text-left">
                                <div class="text-lg font-black text-primary-600">{{ number_format($inventory->price) }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">تومان</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">وضعیت کالا</span>
                            <div class="text-left">
                                @if($inventory->condition == 'new')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                        <i class="ti ti-sparkles"></i>
                                        نو (New)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-recycle"></i>
                                        دست دوم (Used)
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($inventory->color)
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">رنگ</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-1 rounded-full border border-slate-100 shadow-sm">
                                <span class="w-4 h-4 rounded-full border border-slate-200" style="background-color: <?php echo $inventory->color; ?>;"></span>
                                <span class="text-sm font-black text-slate-700">{{ $inventory->color }}</span>
                            </div>
                        </div>
                        @endif

                        @if($inventory->sku)
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">SKU</span>
                            <div class="text-sm font-mono font-bold text-slate-700 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm">
                                {{ $inventory->sku }}
                            </div>
                        </div>
                        @endif

                        @if($inventory->device_code)
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">کد دستگاه</span>
                            <div class="text-sm font-mono font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100 shadow-sm flex items-center gap-2">
                                <i class="ti ti-scan text-blue-500"></i>
                                {{ $inventory->device_code }}
                            </div>
                        </div>
                        @endif

                        @if($inventory->rack_location)
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">موقعیت در انبار</span>
                            <div class="text-sm font-bold text-slate-700 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-sm flex items-center gap-2">
                                <i class="ti ti-map-pin text-slate-400"></i>
                                {{ $inventory->rack_location }}
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($inventory->compatibility_notes)
                    <div class="mt-8 p-6 rounded-[2rem] bg-indigo-50 border border-indigo-100 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h4 class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="ti ti-devices text-indigo-500"></i>
                                سازگاری و مدل‌ها
                            </h4>
                            <p class="text-sm text-indigo-900 leading-relaxed font-medium">{{ $inventory->compatibility_notes }}</p>
                        </div>
                        <i class="ti ti-puzzle absolute -bottom-4 -left-4 text-8xl text-indigo-200/50 -rotate-12 group-hover:scale-110 transition-transform duration-700"></i>
                    </div>
                    @endif

                    @if($inventory->description)
                    <div class="mt-8 p-6 rounded-[2rem] bg-slate-50 border border-slate-100 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="ti ti-notes text-primary-500"></i>
                                توضیحات و یادداشت‌ها
                            </h4>
                            <p class="text-sm text-slate-600 leading-relaxed font-medium">{{ $inventory->description }}</p>
                        </div>
                        <i class="ti ti-quote absolute -bottom-4 -left-4 text-8xl text-slate-200/50 -rotate-12 group-hover:scale-110 transition-transform duration-700"></i>
                    </div>
                    @endif
                </x-enhanced-card>

                <x-enhanced-card title="عملیات انبار" icon="ti-refresh">
                    <form method="POST" action="{{ route('automation.inventory.update-stock', $inventory) }}" class="space-y-4">
                        @csrf
                        <div class="form-group-modern group">
                            <label class="form-label-modern group-focus-within:text-primary-600 text-sm">نوع تراکنش</label>
                            <div class="relative">
                                <select name="transaction_type" class="form-control-modern pr-10 text-sm select2" required>
                                    <option value="purchase">📥 خرید / ورود به انبار</option>
                                    <option value="sale">📤 فروش / خروج از انبار</option>
                                    <option value="use">🛠️ استفاده در تعمیرات</option>
                                    <option value="return">🔄 عودت کالا</option>
                                    <option value="warranty_sent">📦 ارسال به گارانتی</option>
                                    <option value="warranty_return">🔙 بازگشت از گارانتی</option>
                                </select>
                                <i class="ti ti-arrows-exchange absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="form-group-modern group">
                            <label class="form-label-modern group-focus-within:text-primary-600 text-sm">تعداد</label>
                            <div class="relative">
                                <input type="number" name="quantity_change" class="form-control-modern pr-10 text-center text-lg font-bold" min="1" value="1" required>
                                <i class="ti ti-hash absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="form-group-modern group">
                            <label class="form-label-modern group-focus-within:text-primary-600 text-sm">تحویل گیرنده / دهنده</label>
                            <div class="relative">
                                <input type="text" name="receiver" class="form-control-modern pr-10 text-sm" placeholder="نام شخص...">
                                <i class="ti ti-user absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="form-group-modern group">
                            <label class="form-label-modern group-focus-within:text-primary-600 text-sm">ارگان / شرکت</label>
                            <div class="relative">
                                <input type="text" name="organization" class="form-control-modern pr-10 text-sm" placeholder="نام شرکت...">
                                <i class="ti ti-building absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="form-group-modern group">
                            <label class="form-label-modern group-focus-within:text-primary-600 text-sm">بابت / علت</label>
                            <div class="relative">
                                <input type="text" name="reason" class="form-control-modern pr-10 text-sm" placeholder="علت تراکنش...">
                                <i class="ti ti-file-description absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="form-group-modern group">
                            <label class="form-label-modern group-focus-within:text-primary-600 text-sm">توضیحات تکمیلی</label>
                            <div class="relative">
                                <textarea name="notes" class="form-control-modern pr-10 text-sm min-h-[80px]" placeholder="یادداشت..."></textarea>
                                <i class="ti ti-notes absolute right-3 top-4 text-slate-400"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-modern btn-modern-primary w-full justify-center py-3 shadow-lg shadow-primary-500/20 group text-sm">
                            <i class="ti ti-check group-hover:scale-125 transition-transform"></i>
                            <span>ثبت تراکنش</span>
                        </button>
                    </form>
                </x-enhanced-card>
            </div>

            <!-- History and Adjustments -->
            <div class="lg:col-span-2 space-y-8">
                <x-enhanced-card title="تاریخچه تراکنش‌ها" icon="ti-history" animated>
                    <div class="table-container">
                        <x-enhanced-table striped hover responsive>
                            <x-slot name="headers">
                                <th>تاریخ</th>
                                <th>نوع</th>
                                <th>تغییر</th>
                                <th>موجودی</th>
                                <th>توضیحات</th>
                            </x-slot>
                            
                            <x-slot name="rows">
                                @forelse($transactions as $transaction)
                                <tr class="hover-lift group">
                                    <td class="whitespace-nowrap">
                                        <div class="text-sm font-black text-slate-700">
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($transaction->created_at)->format('Y/m/d') }}
                                            @else
                                                {{ $transaction->created_at->format('Y/m/d') }}
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase">
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($transaction->created_at)->format('H:i') }}
                                            @else
                                                {{ $transaction->created_at->format('H:i') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <x-inventory-transaction-type :type="$transaction->transaction_type" />
                                    </td>
                                    <td>
                                        <span class="text-lg font-black {{ $transaction->quantity_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $transaction->quantity_change > 0 ? '+' : '' }}{{ $transaction->quantity_change }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-black text-slate-900 bg-slate-100 px-3 py-1 rounded-lg">{{ $transaction->new_quantity }}</span>
                                    </td>
                                    <td class="max-w-xs">
                                        <div class="flex flex-col gap-1">
                                            @if($transaction->receiver)
                                                <span class="text-[10px] text-slate-500"><i class="ti ti-user"></i> {{ $transaction->receiver }}</span>
                                            @endif
                                            @if($transaction->organization)
                                                <span class="text-[10px] text-slate-500"><i class="ti ti-building"></i> {{ $transaction->organization }}</span>
                                            @endif
                                            <div class="text-xs font-medium truncate group-hover:text-clip group-hover:whitespace-normal transition-all" title="{{ $transaction->notes }}">
                                                <x-inventory-transaction-note
                                                    :note="$transaction->notes"
                                                    :inventory-id="$inventory->id"
                                                    :inventory-url="route('automation.inventory.show', $inventory)"
                                                />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-20">
                                        <div class="flex flex-col items-center justify-center text-slate-300">
                                            <i class="ti ti-history-off text-6xl mb-4 opacity-20"></i>
                                            <p class="font-black uppercase tracking-widest text-xs">هیچ تراکنشی ثبت نشده است</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </x-slot>
                        </x-enhanced-table>
                    </div>

                    @if(method_exists($transactions, 'links') && $transactions->hasPages())
                        <div class="mt-8 pt-8 border-t border-slate-100">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </x-enhanced-card>

                <!-- Manual Adjustments -->
                <div class="bg-gradient-to-br from-amber-500/10 to-orange-600/10 p-8 rounded-[2.5rem] border border-amber-200/50 relative overflow-hidden group">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-white rounded-[1.5rem] flex items-center justify-center text-amber-600 shadow-xl shadow-amber-500/10 group-hover:scale-110 transition-transform duration-500">
                                <i class="ti ti-adjustments text-3xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-900 mb-2">تعدیل دستی موجودی</h4>
                                <p class="text-slate-500 text-sm font-medium max-w-sm">در صورت مغایرت موجودی فیزیکی با سیستم، عدد دقیق را از اینجا اصلاح کنید.</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('automation.inventory.adjust-stock', $inventory) }}" class="flex items-center gap-4 w-full md:w-auto">
                            @csrf
                            <div class="relative flex-1 md:w-32">
                                <input type="number" name="new_quantity" class="form-control-modern text-center font-black text-xl pr-4" value="{{ $inventory->quantity }}" required>
                            </div>
                            <button type="submit" class="btn-modern btn-modern-warning py-4 px-8 shadow-xl shadow-amber-500/20 group/btn">
                                <i class="ti ti-check group-hover/btn:scale-125 transition-transform"></i>
                                <span class="whitespace-nowrap">اعمال تغییر</span>
                            </button>
                        </form>
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                <!-- Danger Zone -->
                <div class="p-8 rounded-[2.5rem] bg-rose-50 border border-rose-100 group relative overflow-hidden">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <h4 class="text-rose-900 font-black text-lg mb-2 flex items-center gap-2">
                                <i class="ti ti-trash"></i>
                                حذف از لیست کالاها
                            </h4>
                            <p class="text-rose-700/70 text-sm font-medium max-w-sm leading-relaxed">با حذف این کالا، تمامی سوابق و تراکنش‌های مرتبط با آن نیز به صورت دائمی از سیستم حذف خواهند شد.</p>
                        </div>
                        <form method="POST" action="{{ route('automation.inventory.destroy', $inventory) }}" class="w-full md:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="btn-modern btn-modern-danger py-4 px-10 w-full group/del shadow-xl shadow-rose-500/20 btn-delete" 
                                    data-item-name="{{ $inventory->name }}">
                                <i class="ti ti-trash group-hover/del:rotate-12 transition-transform"></i>
                                <span>حذف دائمی</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemName = this.getAttribute('data-item-name');
            const form = this.closest('form');
            
            Swal.fire({
                title: 'آیا اطمینان دارید؟',
                text: `کالای "${itemName}" و تمامی سوابق تراکنش آن برای همیشه حذف خواهند شد!`,
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
});
</script>

<style>
    .font-vazir { font-family: 'Vazirmatn', sans-serif !important; }
</style>
@endsection
