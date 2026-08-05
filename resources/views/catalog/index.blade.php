@extends('layouts.shop')

@section('title', 'همه محصولات - پارس لیان')

@section('shop-content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Hero Grain Effect */
    .hero-grain::before {
        content: "";
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0.4;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
    }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }

    .catalog-filter-search {
        position: relative;
    }

    .catalog-filter-search-input {
        width: 100%;
        padding-inline-start: 2.75rem;
        padding-inline-end: 1rem;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        background: #f9fafb;
        border: none;
        border-radius: 1rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        outline: none;
        transition: box-shadow 0.2s, background-color 0.2s;
    }

    .catalog-filter-search-input:focus {
        background: #fff;
        box-shadow: 0 0 0 2px rgb(59 130 246);
    }

    .catalog-filter-search--purple .catalog-filter-search-input:focus {
        box-shadow: 0 0 0 2px rgb(168 85 247);
    }

    .catalog-filter-search-icon {
        position: absolute;
        inset-inline-start: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 1;
        font-size: 1rem;
        line-height: 1;
        color: #9ca3af;
    }

    .catalog-filter-search:focus-within .catalog-filter-search-icon {
        color: rgb(37 99 235);
    }

    .catalog-filter-search--purple:focus-within .catalog-filter-search-icon {
        color: rgb(147 51 234);
    }

    .mobile-filter-drawer {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @media (max-width: 1023px) {
        .mobile-filter-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 70;
            padding: 2rem;
            overflow-y: auto;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mobile-filter-drawer.open,
        .mobile-filter-drawer.active {
            right: 0;
        }
        #filterOverlay {
            z-index: 65;
        }
    }

    #products .catalog-product-card {
        display: flex;
        flex-direction: column;
        min-height: 26rem;
        overflow: hidden;
        border-color: var(--catalog-frame-border) !important;
    }

    #products .catalog-product-card:hover {
        transform: translateY(-4px);
    }

    #products .catalog-product-card__media {
        flex: 0 0 auto;
        position: relative;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        background: #f9fafb;
    }

    #products .catalog-product-card__body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 11.5rem;
        padding: 1.25rem 1.5rem;
    }

    #products .catalog-product-card__title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.75rem;
        line-height: 1.375rem;
    }

    #products .catalog-product-card__stock {
        min-height: 2rem;
    }

    #products .catalog-product-card__footer {
        margin-top: auto;
        flex-shrink: 0;
        padding-top: 1rem;
        border-top: 1px solid #f9fafb;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }

    #products .catalog-product-card__footer-actions {
        width: 100%;
        justify-content: space-between;
    }

    .product-qty-control .product-qty {
        display: inline-block;
        min-width: 3rem;
        line-height: 2.75rem;
        user-select: none;
    }

    .catalog-category-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.625rem 0.875rem;
        border-radius: 0.875rem;
        border: 1.5px solid var(--catalog-frame-border);
        background: #fff;
        cursor: pointer;
        transition: background-color 0.2s, border-color 0.2s, color 0.2s;
    }

    .catalog-category-option:hover {
        background: #f8fafc;
    }

    .catalog-category-option.is-selected {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
        border-color: #93c5fd;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08);
    }

    .catalog-category-option.is-selected .catalog-category-option__text {
        color: #1d4ed8;
        font-weight: 800;
    }

    .catalog-category-option__icon {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.875rem;
        transition: background-color 0.2s, color 0.2s;
    }

    .catalog-category-option.is-selected .catalog-category-option__icon {
        background: #2563eb;
        color: #fff;
    }

    .catalog-category-option__text {
        flex: 1;
        min-width: 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .catalog-price-input {
        width: 100%;
        min-width: 0;
        padding: 0.75rem 0.875rem;
        background: #fff;
        border: 1.5px solid var(--catalog-frame-border);
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #111827;
        box-shadow: none;
        transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    .catalog-price-input::placeholder {
        color: #9ca3af;
        font-weight: 500;
    }

    .catalog-price-input:hover {
        border-color: #d1d5db;
    }

    .catalog-price-input:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        background: #fffbeb;
    }

    #filterForm .money-input-words {
        display: none;
    }

    :root {
        --catalog-frame-border: #57534e;
    }

    #filterForm .catalog-filter-search-input,
    #filterForm .catalog-category-option,
    #filterForm .catalog-price-input,
    #filterForm .catalog-filter-select,
    #filterForm .catalog-filter-toggle-row,
    #filterForm .category-tree-toggle,
    #filterForm .catalog-filter-clear {
        border: 1.5px solid var(--catalog-frame-border);
    }

    #filterForm .catalog-filter-search-input {
        background: #fff;
        border: 1.5px solid var(--catalog-frame-border);
    }

    #filterForm .catalog-filter-search-input:focus {
        box-shadow: 0 0 0 3px rgba(87, 83, 78, 0.12);
    }

    #filterForm .catalog-category-option {
        border-color: var(--catalog-frame-border);
        background: #fff;
    }

    #filterForm .catalog-category-option.is-selected {
        border-color: #2563eb;
    }

    #filterForm .catalog-price-input {
        border-color: var(--catalog-frame-border);
    }

    #filterForm .catalog-filter-select {
        width: 100%;
        padding: 0.75rem 2.5rem 0.75rem 1rem;
        background: #fff;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
        outline: none;
        appearance: none;
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    #filterForm .catalog-filter-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
    }

    #filterForm .catalog-filter-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        background: #fff;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    #filterForm .catalog-filter-toggle-row:hover {
        background: #fafaf9;
    }

    #filterForm .category-tree-toggle {
        background: #fff;
        border-radius: 0.625rem;
    }

    #filterForm .catalog-filter-clear {
        background: #fff;
    }

    #products .product-qty-control,
    #products .catalog-product-card .btn-add-to-cart:not(:disabled),
    #products .catalog-product-card__stock > span {
        border: 1.5px solid var(--catalog-frame-border) !important;
    }

    #products .catalog-product-card__footer-actions > .w-12.cursor-not-allowed {
        border: 1.5px solid var(--catalog-frame-border);
    }
</style>

<!-- Catalog Hero Section -->
<div class="relative bg-[#050505] pt-20 pb-16 md:pt-24 md:pb-20 overflow-hidden">
    <!-- Dynamic Mesh Gradient Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-20%] right-[-10%] w-[60%] h-[60%] bg-blue-600/20 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[50%] h-[50%] bg-purple-600/15 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <!-- Content Side -->
            <div class="flex-[1.2] text-right order-2 lg:order-1">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl mb-5 fade-in-up">
                    <span class="flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-1.5 w-1.5 rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-blue-400 tracking-wide">راهکارهای پیشرفته سخت‌افزار</span>
                </div>

                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-3 leading-tight tracking-tight fade-in-up">
                    قدرت در <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-l from-blue-400 to-purple-500">دستان</span> شماست
                </h1>

                <p class="text-gray-400 text-base md:text-lg max-w-xl leading-relaxed mb-4 fade-in-up" style="transition-delay: 0.1s">
                    ما در پارس لیان، مرزهای تکنولوژی را برای شما جابه‌جا می‌کنیم. دسترسی به برترین برندهای جهانی با گارانتی طلایی و پشتیبانی فنی ۲۴ ساعته.
                </p>

                <div class="flex flex-wrap justify-end items-center gap-5 -mt-1 fade-in-up" style="transition-delay: 0.2s">
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-white font-black text-xl md:text-2xl tracking-tighter">15K+</p>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">قطعات موجود</p>
                        </div>
                        <div class="w-px h-10 bg-white/10"></div>
                        <div class="text-right">
                            <p class="text-white font-black text-xl md:text-2xl tracking-tighter">100%</p>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">تضمین اصالت</p>
                        </div>
                    </div>
                    
                    <a href="#products" class="group relative px-8 py-3.5 bg-blue-600 text-white rounded-xl font-black text-sm md:text-base overflow-hidden transition-all hover:scale-105 active:scale-95">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <span class="relative z-10 flex items-center gap-3">
                            <i class="ti ti-arrow-right-circle text-xl"></i>
                            کاوش در محصولات
                        </span>
                    </a>
                </div>
            </div>

            <!-- Visual Side -->
            <div class="flex-1 order-1 lg:order-2 fade-in-up" style="transition-delay: 0.3s">
                <div class="relative">
                    <!-- Main Card -->
                    <div class="relative z-20 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 md:p-6 shadow-xl overflow-hidden group">
                        <div class="absolute -top-12 -right-12 w-40 h-40 bg-blue-500/15 rounded-full blur-[60px] group-hover:bg-blue-500/25 transition-colors duration-500"></div>

                        <div class="relative z-10">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="ti ti-device-desktop-analytics text-lg text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg md:text-xl font-black text-white leading-snug">پایداری مطلق در اوج سرعت</h2>
                                    <p class="text-gray-400 text-xs leading-relaxed mt-1.5">انتخاب حرفه‌ای‌ها برای رندرینگ، گیمینگ و هوش مصنوعی.</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white/5 rounded-lg border border-white/10 text-[11px] font-bold text-white">
                                    <i class="ti ti-shield-check text-blue-400"></i>
                                    گارانتی طلایی
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white/5 rounded-lg border border-white/10 text-[11px] font-bold text-white">
                                    <i class="ti ti-truck-delivery text-purple-400"></i>
                                    ارسال اکسپرس
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white min-h-screen py-12" id="products" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-1/4 fade-in-up">
                <!-- Mobile Filter Trigger -->
                <div class="lg:hidden mb-6">
                    <button id="openFilters" class="w-full flex items-center justify-center gap-3 bg-white border-[1.5px] border-[#57534e] p-4 rounded-2xl font-bold text-gray-700 shadow-sm hover:shadow-md transition-all duration-300 group">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i class="ti ti-adjustments-horizontal text-xl"></i>
                        </div>
                        فیلترها و مرتب‌سازی
                    </button>
                </div>

                <div id="filterOverlay" class="fixed inset-0 bg-black/40 hidden lg:hidden"></div>

                <div id="filterDrawer" class="mobile-filter-drawer lg:block">
                    <div class="lg:hidden flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-black text-gray-900 flex items-center">
                            <i class="ti ti-adjustments-horizontal ml-3 text-blue-600"></i>
                            فیلترها
                        </h2>
                        <button id="closeFilters" aria-label="بستن فیلترها" class="w-10 h-10 bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="ti ti-x text-xl"></i>
                        </button>
                    </div>

                    <!-- Filter Form Section (Collapsible) -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-28">
                        <!-- Desktop Header (Always Visible) -->
                        <div class="hidden lg:flex items-center justify-between p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-sm font-black text-gray-900 flex items-center uppercase tracking-widest">
                                <i class="ti ti-adjustments-horizontal ml-3 text-blue-600"></i>
                                فیلترها
                            </h3>
                            @if(request()->anyFilled(['category', 'min_price', 'max_price', 'in_stock', 'featured']))
                                <a href="{{ route('catalog.index') }}" class="text-[10px] font-bold text-red-500 hover:underline">
                                    پاک کردن همه
                                </a>
                            @endif
                        </div>

                        <form action="{{ route('catalog.index') }}" method="GET" id="filterForm" class="p-8 space-y-10">
                            <!-- Category Filter -->
                            <div class="filter-section">
                                <h3 class="text-sm font-black text-gray-900 mb-6 flex items-center uppercase tracking-widest">
                                    <span class="w-2 h-6 bg-blue-600 rounded-full ml-3"></span>
                                    دسته‌بندی‌ها
                                </h3>
                                <div class="catalog-filter-search mb-4">
                                    <input type="search"
                                           placeholder="جستجوی دسته‌بندی..."
                                           autocomplete="off"
                                           class="catalog-filter-search-input filter-search"
                                           data-target="category-list">
                                    <i class="ti ti-search catalog-filter-search-icon"></i>
                                </div>
                                <div class="space-y-1 max-h-72 overflow-y-auto custom-scrollbar pl-2" id="category-list">
                                    @php
                                        $rawSelectedCategory = request('category');
                                        $selectedCategoryId = is_array($rawSelectedCategory) ? ($rawSelectedCategory[0] ?? null) : $rawSelectedCategory;
                                    @endphp
                                    <label class="catalog-category-option filter-item mb-2 {{ blank($selectedCategoryId) ? 'is-selected' : '' }}" data-name="همه">
                                        <input type="radio"
                                               name="category"
                                               value=""
                                               {{ blank($selectedCategoryId) ? 'checked' : '' }}
                                               class="sr-only"
                                               onchange="this.form.submit()">
                                        <span class="catalog-category-option__icon">
                                            <i class="ti ti-layout-grid"></i>
                                        </span>
                                        <span class="catalog-category-option__text truncate">همه دسته‌بندی‌ها</span>
                                    </label>
                                    @foreach($categoryTree as $category)
                                        @include('catalog.partials.category-filter-node', ['category' => $category, 'depth' => 0])
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Filter -->
                            <div class="filter-section">
                                <h3 class="text-sm font-black text-gray-900 mb-6 flex items-center uppercase tracking-widest">
                                    <span class="w-2 h-6 bg-amber-500 rounded-full ml-3"></span>
                                    محدوده قیمت
                                </h3>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-3">
                                        <div>
                                            <label for="catalog-min-price" class="block text-xs font-bold text-gray-600 mb-1.5">از (تومان)</label>
                                            <input type="text"
                                                   id="catalog-min-price"
                                                   name="min_price"
                                                   value="{{ request('min_price') }}"
                                                   placeholder="حداقل قیمت"
                                                   inputmode="numeric"
                                                   autocomplete="off"
                                                   data-money-input
                                                   class="catalog-price-input">
                                        </div>
                                        <div>
                                            <label for="catalog-max-price" class="block text-xs font-bold text-gray-600 mb-1.5">تا (تومان)</label>
                                            <input type="text"
                                                   id="catalog-max-price"
                                                   name="max_price"
                                                   value="{{ request('max_price') }}"
                                                   placeholder="حداکثر قیمت"
                                                   inputmode="numeric"
                                                   autocomplete="off"
                                                   data-money-input
                                                   class="catalog-price-input">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-2xl hover:bg-amber-600 transition-all duration-300 font-bold shadow-lg shadow-amber-100 hover:shadow-xl hover:-translate-y-1">
                                        اعمال محدوده قیمت
                                    </button>
                                </div>
                            </div>

                            <!-- Sort Options -->
                            <div class="filter-section">
                                <h3 class="text-sm font-black text-gray-900 mb-6 flex items-center uppercase tracking-widest">
                                    <span class="w-2 h-6 bg-orange-500 rounded-full ml-3"></span>
                                    مرتب‌سازی
                                </h3>
                                <div class="relative">
                                    <select name="sort" onchange="this.form.submit()" aria-label="مرتب‌سازی"
                                            class="catalog-filter-select">
                                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>جدیدترین</option>
                                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>ارزان‌ترین</option>
                                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>گران‌ترین</option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>قدیمی‌ترین</option>
                                    </select>
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Toggle Filters -->
                            <div class="filter-section space-y-4 pt-4 border-t border-gray-50">
                                <input type="hidden" name="show_all" id="catalog_show_all" value="{{ request()->boolean('show_all') ? '1' : '0' }}">
                                <label class="catalog-filter-toggle-row group cursor-pointer">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-blue-600 transition-colors">فقط کالاهای موجود</span>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" name="in_stock" value="1" {{ !request()->boolean('show_all') ? 'checked' : '' }}
                                               class="peer sr-only" id="catalog_in_stock_toggle"
                                               onchange="document.getElementById('catalog_show_all').value = this.checked ? '0' : '1'; this.form.submit();">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </div>
                                </label>
                                <label for="catalog_featured_toggle" class="catalog-filter-toggle-row group cursor-pointer">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-purple-600 transition-colors">کالاهای پیشنهادی</span>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" name="featured" value="1" id="catalog_featured_toggle"
                                               {{ request()->boolean('featured') ? 'checked' : '' }}
                                               class="peer sr-only"
                                               onchange="this.form.submit()">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                    </div>
                                </label>
                            </div>

                            @if(request()->anyFilled(['category', 'min_price', 'max_price', 'in_stock', 'featured']))
                            <a href="{{ route('catalog.index') }}" class="catalog-filter-clear flex items-center justify-center gap-2 w-full py-4 text-red-600 rounded-2xl hover:bg-red-50 transition-all duration-300 font-bold text-sm">
                                <i class="ti ti-trash"></i>
                                حذف همه فیلترها
                            </a>
                            @endif
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Product List -->
            <div class="flex-1">
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($products as $product)
                    <div class="fade-in-stagger group relative product-card catalog-product-card bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 flex flex-col h-full overflow-hidden">
                        <div class="absolute top-5 right-5 z-10 flex flex-col gap-2">
                            @if($product->is_featured)
                            <span class="bg-blue-600 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-blue-200">پیشنهادی</span>
                            @endif
                            @if($product->is_new)
                            <span class="bg-emerald-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-emerald-200">جدید</span>
                            @endif
                        </div>

                        @if($product->stock_quantity <= 0)
                        <div class="absolute top-5 left-5 z-10">
                            <span class="bg-gray-900/80 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest">ناموجود</span>
                        </div>
                        @endif

                        <div class="catalog-product-card__media relative">
                            <img loading="lazy" src="{{ $product->main_image_url }}"
                                 alt="{{ $product->name }}"
                                 class="absolute inset-0 z-[1] w-full h-full object-contain p-6 group-hover:scale-110 transition-transform duration-700 ease-out"
                                 onerror="this.onerror=null;this.src='<?php echo asset('images/no-image.svg'); ?>';">

                            <div class="absolute inset-0 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                                <div class="absolute inset-0 bg-blue-600/5 pointer-events-none"></div>
                                <div class="absolute inset-0 flex items-center justify-center gap-3 pointer-events-none">
                                    <a href="{{ route('catalog.show', $product->slug) }}"
                                       class="product-card-action relative z-30 pointer-events-auto w-12 h-12 bg-white text-gray-900 rounded-2xl flex items-center justify-center shadow-xl hover:bg-blue-600 hover:text-white transition-all duration-300 transform translate-y-4 group-hover:translate-y-0"
                                       title="مشاهده جزئیات">
                                        <i class="ti ti-eye text-xl"></i>
                                    </a>
                                    <button type="button"
                                            data-wishlist-slug="{{ $product->slug }}"
                                            data-wishlist-name="{{ $product->name }}"
                                            data-wishlist-image="{{ $product->main_image_url }}"
                                            class="wishlist-add-btn product-card-action relative z-30 pointer-events-auto w-12 h-12 bg-white text-gray-900 rounded-2xl flex items-center justify-center shadow-xl hover:bg-red-500 hover:text-white transition-all duration-300 transform translate-y-4 group-hover:translate-y-0 delay-75"
                                            title="افزودن به علاقه‌مندی‌ها">
                                        <i class="ti ti-heart text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="catalog-product-card__body">
                            <div class="mb-3">
                                @php $listCategory = $product->category?->parent ?? $product->category; @endphp
                                @if($listCategory)
                                <a href="{{ route('catalog.index', ['category' => $listCategory->id]) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline mb-2 block">
                                    {{ $listCategory->name }}
                                </a>
                                @endif
                                <h3 class="catalog-product-card__title text-lg font-black text-gray-900 group-hover:text-blue-600 transition-colors">
                                    <a href="{{ route('catalog.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                            </div>

                            <div class="catalog-product-card__stock mb-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-black px-3 py-1.5 rounded-full {{ $product->stock_quantity > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    <i class="ti ti-box"></i>
                                    @if($product->stock_quantity > 0)
                                        موجود: {{ $product->stock_quantity }} عدد
                                    @else
                                        اتمام موجودی
                                    @endif
                                </span>
                            </div>

                            <div class="catalog-product-card__footer">
                                <div class="flex flex-col min-w-0">
                                    @if($product->is_on_sale)
                                    <span class="text-xs text-gray-400 line-through font-bold mb-1">{{ number_format($product->price) }} تومان</span>
                                    <span class="text-xl font-black text-blue-600">{{ number_format($product->sale_price) }} <span class="text-xs">تومان</span></span>
                                    @else
                                    <span class="text-xl font-black text-gray-900">{{ number_format($product->price) }} <span class="text-xs text-gray-400 font-bold">تومان</span></span>
                                    @endif
                                </div>

                                @if($product->stock_quantity > 0)
                                <div class="catalog-product-card__footer-actions flex items-center gap-2 flex-shrink-0">
                                    <div class="product-qty-control flex items-center bg-white rounded-2xl overflow-hidden" data-qty-control>
                                        <button type="button" class="qty-btn-minus w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors" data-action="minus" aria-label="کاهش">
                                            <i class="ti ti-minus"></i>
                                        </button>
                                        <span class="product-qty w-12 text-center font-black text-sm select-none" data-min="1" data-max="{{ $product->stock_quantity }}" aria-live="polite">1</span>
                                        <button type="button" class="qty-btn-plus w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors" data-action="plus" aria-label="افزایش">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>
                                    <button class="btn-add-to-cart w-12 h-12 bg-gray-900 text-white rounded-2xl flex items-center justify-center hover:bg-blue-600 transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-blue-200"
                                            data-product-slug="{{ $product->slug }}"
                                            data-max-qty="{{ $product->stock_quantity }}"
                                            aria-label="افزودن به سبد خرید">
                                        <i class="ti ti-shopping-cart text-xl"></i>
                                    </button>
                                </div>
                                @else
                                <div class="catalog-product-card__footer-actions flex items-center justify-end">
                                <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center cursor-not-allowed">
                                    <i class="ti ti-shopping-cart-off text-xl"></i>
                                </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-16">
                    {{ $products->links() }}
                </div>
                @else
                <div class="bg-white rounded-[3rem] p-20 text-center shadow-sm border border-gray-100">
                    <div class="w-32 h-32 bg-blue-50 text-blue-600 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 animate-bounce">
                        <i class="ti ti-search-off text-5xl"></i>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">محصولی پیدا نشد!</h3>
                    <p class="text-gray-500 text-lg mb-10 max-w-md mx-auto">متأسفانه با فیلترهای انتخابی شما، محصولی در این دسته یافت نشد. لطفاً فیلترها را تغییر دهید.</p>
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-3 bg-gray-900 text-white px-10 py-5 rounded-2xl font-black hover:bg-blue-600 transition-all duration-300 shadow-xl shadow-gray-200 hover:shadow-blue-200 hover:-translate-y-1">
                        مشاهده همه محصولات
                        <i class="ti ti-arrow-left text-xl"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="{{ asset('js/money-input.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = { threshold: 0.1 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        const openFilters = document.getElementById('openFilters');
        const closeFilters = document.getElementById('closeFilters');
        const filterDrawer = document.getElementById('filterDrawer');
        const filterOverlay = document.getElementById('filterOverlay');
        const filterDrawerHost = filterDrawer?.parentElement;

        function isMobileFilter() {
            return window.matchMedia('(max-width: 1023px)').matches;
        }

        function mountFilterPortal() {
            if (!isMobileFilter()) return;
            if (filterOverlay && filterOverlay.parentElement !== document.body) {
                document.body.appendChild(filterOverlay);
            }
            if (filterDrawer && filterDrawer.parentElement !== document.body) {
                document.body.appendChild(filterDrawer);
            }
        }

        function restoreFilterPortal() {
            if (!filterDrawerHost) return;
            if (filterOverlay && filterOverlay.parentElement === document.body) {
                filterDrawerHost.insertBefore(filterOverlay, filterDrawerHost.firstChild);
            }
            if (filterDrawer && filterDrawer.parentElement === document.body) {
                filterDrawerHost.appendChild(filterDrawer);
            }
        }

        function openFilterDrawer() {
            mountFilterPortal();
            filterDrawer?.classList.add('open', 'active');
            filterOverlay?.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeFilterDrawer() {
            filterDrawer?.classList.remove('open', 'active');
            filterOverlay?.classList.add('hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            restoreFilterPortal();
        }

        openFilters?.addEventListener('click', openFilterDrawer);
        closeFilters?.addEventListener('click', closeFilterDrawer);
        filterOverlay?.addEventListener('click', closeFilterDrawer);

        window.addEventListener('resize', function () {
            if (!isMobileFilter()) {
                closeFilterDrawer();
            }
        });

        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';

        document.querySelectorAll('.wishlist-add-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleWishlist(btn.dataset.wishlistSlug, btn.dataset.wishlistName, btn.dataset.wishlistImage);
            });
        });

        function bindFilterTreeToggle(buttonSelector, chevronSelector) {
            document.querySelectorAll(buttonSelector).forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const target = document.getElementById(btn.dataset.target);
                    const chevron = btn.querySelector(chevronSelector);
                    if (!target) return;
                    const willOpen = target.classList.contains('hidden');
                    target.classList.toggle('hidden');
                    chevron?.classList.toggle('rotate-180', willOpen);
                    btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                });
            });
        }

        bindFilterTreeToggle('.category-tree-toggle', '.category-tree-chevron');

        const filterSearches = document.querySelectorAll('.filter-search');
        filterSearches.forEach(input => {
            input.addEventListener('input', function() {
                const targetId = this.dataset.target;
                const searchTerm = this.value.toLowerCase();
                const container = document.getElementById(targetId);
                if (!container) return;
                const items = container.querySelectorAll('.filter-item');

                items.forEach(item => {
                    const text = (item.dataset.name || '').toLowerCase();
                    item.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });

        const priceInputs = document.querySelectorAll('input[type="number"][name*="price"]');
        priceInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 0) this.value = 0;
            });
        });
    });
</script>
@endpush
@endsection
