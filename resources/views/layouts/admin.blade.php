<!DOCTYPE html>
<html dir="rtl" lang="fa" class="admin-layout-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="{{ config('app.viewport') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="money-words-url" content="{{ route('automation.money.words') }}">
    @endauth
    <title>@yield('title', 'پنل مدیریت - پارس لیان')</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb" id="themeMeta">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="{{ \App\Support\BrandLogo::exists() ? \App\Support\BrandLogo::url() : asset('assets/images/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ParsLian">

    <!-- Fonts & Icons -->
    <link href="{{ asset('fonts/vazirmatn/Vazirmatn-font-face.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/partials/form-enhancements.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @if(\App\Support\BrandLogo::exists())
    <link rel="icon" type="image/png" href="{{ \App\Support\BrandLogo::url() }}">
    @endif



    @yield('styles')
    @stack('styles')
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <style>
    /* Admin Layout Styles - Horizontal Navigation & Sidebar */
    :root {
        --admin-header-height: 80px;
        --sidebar-width: 280px;
        --bg-body: #f8fafc;
    }

    html {
        height: 100%;
        overflow: hidden;
    }

    .admin-layout {
        font-family: 'Vazirmatn', Tahoma, sans-serif;
        direction: rtl;
        background-color: var(--bg-body);
        color: #0f172a;
        margin: 0;
        height: 100%;
        overflow: hidden !important;
    }

    /* Shell fills viewport below fixed header */
    .admin-wrapper {
        position: fixed;
        top: var(--admin-header-height);
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        width: 100%;
        min-height: 0;
        overflow: hidden;
        z-index: 1;
        background-color: var(--bg-body);
    }

    /* Modern Top Header — always pinned to viewport top */
    .admin-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--admin-header-height);
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .header-container {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .admin-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 800;
        background: linear-gradient(to left, #fff, #94a3b8);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-decoration: none;
    }

    /* Sidebar Navigation */
    .admin-sidebar {
        width: var(--sidebar-width);
        background: #1e293b;
        border-left: 1px solid rgba(255,255,255,0.1);
        overflow-y: scroll;
        scrollbar-gutter: stable;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        z-index: 990;
        position: fixed;
        right: calc(var(--sidebar-width) * -1);
        top: var(--admin-header-height);
        bottom: 0;
    }

    .admin-sidebar.open {
        right: 0;
    }

    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 980;
        display: none;
    }

    .sidebar-overlay.show {
        display: block;
    }

    .sidebar-menu {
        padding: 1.5rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .menu-group {
        margin-bottom: 1.5rem;
    }

    .group-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.55);
        letter-spacing: 0;
        padding: 0 0.75rem 0.4rem;
        display: block;
    }

    .nav-link-modern {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: rgba(255,255,255,0.9);
        text-decoration: none;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-link-modern:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-link-modern.active {
        background: #3b82f6;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .nav-link-modern i {
        font-size: 1.25rem;
    }

    .sidebar-search {
        margin-bottom: 1rem;
    }

    .sidebar-search-input {
        width: 100%;
        padding: 0.65rem 2.25rem 0.65rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        font-size: 0.85rem;
        outline: none;
    }

    .sidebar-search-input::placeholder {
        color: rgba(255, 255, 255, 0.45);
    }

    .sidebar-search-input:focus {
        border-color: rgba(59, 130, 246, 0.6);
        background: rgba(255, 255, 255, 0.12);
    }

    .sidebar-search-wrap {
        position: relative;
    }

    .sidebar-search-wrap i {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.45);
        pointer-events: none;
    }

    /* Content Area — this element scrolls */
    .admin-main {
        flex: 1 1 auto;
        width: 100%;
        min-height: 0;
        max-height: 100%;
        overflow-y: scroll;
        overflow-x: hidden;
        scrollbar-gutter: stable;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        background-color: var(--bg-body);
    }

    body.admin-layout.sidebar-open .admin-main {
        overflow: hidden;
    }

    .admin-content {
        padding: 1.25rem 1.5rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
        box-sizing: border-box;
    }

    .admin-content .min-h-\[calc\(100vh-12rem\)\] {
        min-height: auto !important;
    }

    /* Modals/overlays inside the scroll pane must not inflate scroll height */
    .admin-main .fixed.inset-0.hidden,
    .admin-main [id$="Modal"].hidden {
        display: none !important;
    }

    .admin-main .min-h-screen {
        min-height: 0 !important;
    }

    .admin-content > .p-6 {
        padding: 0 !important;
    }

    /* Breadcrumb */
    .breadcrumb-container {
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .breadcrumb-item:not(:last-child)::after {
        content: "\eb0b"; /* tabler chevron-left */
        font-family: 'tabler-icons';
        font-size: 0.75rem;
        opacity: 0.5;
    }

    .breadcrumb-link {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
        cursor: pointer;
    }

    .breadcrumb-link:hover {
        color: var(--color-primary-600);
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .breadcrumb-current {
        color: var(--text-main);
        font-weight: 600;
    }

    /* User Profile & Actions */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-profile-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.4rem;
        padding-left: 1rem;
        background: rgba(0,0,0,0.02);
        border: 1px solid var(--border-base);
        border-radius: 2rem;
        cursor: pointer;
        transition: all 0.2s;
        font: inherit;
        color: inherit;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: var(--color-primary-600);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Mobile Styles - Handled by general fixed sidebar now */

    /* Animations */
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Button Loading State */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
        border: 2px solid currentColor;
        border-radius: 50%;
        border-right-color: transparent;
        animation: button-loading-spinner 0.75s linear infinite;
    }

    @keyframes button-loading-spinner {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Admin layout overrides */
    </style>
</head>
<body class="admin-layout">
    @php
        $user = auth()->user();
        $routePrefix = 'automation.'; // پیش‌فرض

        if (request()->routeIs('super-admin.*')) {
            $routePrefix = 'super-admin.';
            $dashboardRoute = route('super-admin.dashboard');
        } elseif (request()->routeIs('admin.*')) {
            $routePrefix = 'admin.';
            $dashboardRoute = route('admin.dashboard');
        } elseif (request()->routeIs('automation.*')) {
            $routePrefix = 'automation.';
            $dashboardRoute = route('automation.dashboard');
        } else {
            $routePrefix = ($user->isAdmin() || $user->isSuperAdmin()) ? 'admin.' : 'automation.';
            $dashboardRoute = route($routePrefix . 'dashboard');
        }
    @endphp

    <!-- Header Navigation -->
    <header class="admin-header">
        <div class="header-container">
            <!-- Logo & Toggle -->
            <div class="flex items-center gap-4">
                <button class="p-2 text-slate-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors" id="navToggle">
                    <i class="ti ti-menu-2 text-2xl"></i>
                </button>
                <a href="{{ $dashboardRoute }}" class="admin-logo">
                    <x-brand-logo size="admin" mode="web" class="h-9 max-w-[140px] rounded-lg" />
                </a>
            </div>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Quick Shortcuts -->
                <div class="hidden sm:flex items-center gap-2 border-l border-slate-700/50 pl-4 ml-4">
                    @if($user->isReceptionist() || $user->isAdmin())
                        <a href="{{ route('automation.customers.create') }}" class="p-2 text-slate-400 hover:bg-white/10 hover:text-white rounded-lg transition-all" title="ثبت مشتری">
                            <i class="ti ti-user-plus text-xl"></i>
                        </a>
                        <a href="{{ route('automation.service-orders.create') }}" class="p-2 text-slate-400 hover:bg-white/10 hover:text-white rounded-lg transition-all" title="ثبت پذیرش">
                            <i class="ti ti-file-plus text-xl"></i>
                        </a>
                    @endif
                    @if($user->isTechnician())
                        <a href="{{ route('automation.repairs.index') }}" class="p-2 text-slate-400 hover:bg-white/10 hover:text-white rounded-lg transition-all" title="لیست تعمیرات">
                            <i class="ti ti-tool text-xl"></i>
                        </a>
                    @endif
                </div>



                <!-- User Profile Dropdown -->
                <div class="relative" id="userProfileDropdown">
                    <button type="button" id="userProfileToggle" class="user-profile-btn cursor-pointer transition-all hover:bg-white/10 border border-transparent" style="background: rgba(255,255,255,0.05);">
                        <div class="user-avatar">
                            @php
                                $initials = '';
                                $nameParts = explode(' ', Auth::user()->name);
                                if (count($nameParts) >= 1) {
                                    $initials = mb_substr($nameParts[0], 0, 1);
                                    if (count($nameParts) >= 2) {
                                        $initials .= mb_substr($nameParts[1], 0, 1);
                                    }
                                }
                            @endphp
                            {{ $initials ?: 'A' }}
                        </div>
                        <div class="hidden sm:flex flex-col items-start leading-tight">
                            <span class="text-xs font-bold text-white">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ Auth::user()->getRoleDisplayName() }}</span>
                        </div>
                        <i class="ti ti-chevron-down text-slate-400 text-xs mr-2 transition-transform duration-200" id="userProfileChevron"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="userProfileMenu"
                         class="hidden absolute left-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">
                        
                        <div class="p-3 border-b border-slate-50 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                    {{ $initials ?: 'A' }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</span>
                                    <span class="text-xs text-slate-500">{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-2 space-y-1">
                            <a href="{{ route('automation.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-all group">
                                <i class="ti ti-layout-dashboard text-lg text-slate-400 group-hover:text-primary-500 transition-colors"></i>
                                <span>میز کار (داشبورد)</span>
                            </a>
                            
                            <!--
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all group">
                                <i class="ti ti-user-circle text-lg text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span>پروفایل کاربری</span>
                            </a>
                            -->
                            
                            <div class="h-px bg-slate-100 my-1"></div>
                            
                            <a href="{{ route('logout') }}" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-rose-600 hover:bg-rose-50 rounded-xl transition-all group" onclick="return confirm('آیا از خروج مطمئن هستید؟')">
                                <i class="ti ti-logout-2 text-lg text-rose-400 group-hover:text-rose-500 transition-colors"></i>
                                <span>خروج از حساب</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <x-page-loader logo-size="admin" logo-class="h-12 max-w-[160px] rounded-lg" />

    <div class="admin-wrapper">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" id="adminSidebar">
            @include('layouts.partials.admin-sidebar-menu')
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main">
            <div class="admin-content">
                <x-admin-breadcrumb :route-prefix="$routePrefix" />

                @if(request()->routeIs('automation.dashboard', 'admin.dashboard', 'super-admin.dashboard'))
                    <x-welcome-banner />
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <x-notification />

    @if(session()->has('success'))
        @php $message = session()->pull('success'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification({
                    type: 'success',
                    title: 'عملیات موفق',
                    message: "{{ e($message) }}"
                });
            });
        </script>
    @endif

    @if(session()->has('error'))
        @php $message = session()->pull('error'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification({
                    type: 'error',
                    title: 'خطا',
                    message: "{{ e($message) }}"
                });
            });
        </script>
    @endif

    @if(session()->has('warning'))
        @php $message = session()->pull('warning'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification({
                    type: 'warning',
                    title: 'هشدار',
                    message: "{{ e($message) }}"
                });
            });
        </script>
    @endif

    @if(session()->has('info'))
        @php $message = session()->pull('info'); @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification({
                    type: 'info',
                    title: 'اطلاعیه',
                    message: "{{ e($message) }}"
                });
            });
        </script>
    @endif

    <script>
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const profileToggle = document.getElementById('userProfileToggle');
            const profileMenu = document.getElementById('userProfileMenu');
            const profileChevron = document.getElementById('userProfileChevron');
            const profileDropdown = document.getElementById('userProfileDropdown');

            if (profileToggle && profileMenu) {
                profileToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                    profileChevron?.classList.toggle('rotate-180');
                });
                document.addEventListener('click', function (e) {
                    if (profileDropdown && !profileDropdown.contains(e.target)) {
                        profileMenu.classList.add('hidden');
                        profileChevron?.classList.remove('rotate-180');
                    }
                });
            }

            const navToggle = document.getElementById('navToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (navToggle && adminSidebar) {
                const toggleIcon = navToggle.querySelector('i');

                navToggle.addEventListener('click', function() {
                    adminSidebar.classList.toggle('open');
                    if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
                    document.body.classList.toggle('sidebar-open', adminSidebar.classList.contains('open'));

                    if (toggleIcon) {
                        if (adminSidebar.classList.contains('open')) {
                            toggleIcon.classList.remove('ti-menu-2');
                            toggleIcon.classList.add('ti-x');
                        } else {
                            toggleIcon.classList.remove('ti-x');
                            toggleIcon.classList.add('ti-menu-2');
                        }
                    }
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (adminSidebar) {
                        adminSidebar.classList.remove('open');
                        document.body.classList.remove('sidebar-open');
                    }
                    sidebarOverlay.classList.remove('show');
                    const toggleIcon = navToggle ? navToggle.querySelector('i') : null;
                    if (toggleIcon) {
                        toggleIcon.classList.remove('ti-x');
                        toggleIcon.classList.add('ti-menu-2');
                    }
                });
            }

            const adminSidebarSearch = document.getElementById('adminSidebarSearch');
            if (adminSidebarSearch) {
                adminSidebarSearch.addEventListener('input', function () {
                    const query = this.value.trim().toLowerCase();
                    document.querySelectorAll('#adminSidebar .menu-group').forEach(function (group) {
                        let visibleLinks = 0;
                        group.querySelectorAll('.nav-link-modern').forEach(function (link) {
                            const label = (link.dataset.searchLabel || link.textContent || '').toLowerCase();
                            const match = query === '' || label.includes(query);
                            link.style.display = match ? '' : 'none';
                            if (match) visibleLinks++;
                        });
                        group.style.display = visibleLinks > 0 ? '' : 'none';
                    });
                });
            }

            // Auto-loading for forms
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !this.classList.contains('no-loading')) {
                        submitBtn.classList.add('btn-loading');
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/money-input.js') }}"></script>
    @stack('modals')
    @yield('scripts')
    @stack('scripts')
    @include('partials.scroll-restore')

    <script>
        (function () {
            function hideLoaderNow() {
                var loader = document.getElementById('pageLoader');
                if (!loader) return;
                loader.classList.add('hide');
                loader.style.display = 'none';
                loader.style.pointerEvents = 'none';
            }

            hideLoaderNow();
            document.addEventListener('DOMContentLoaded', hideLoaderNow);
            window.addEventListener('load', hideLoaderNow);
            setTimeout(hideLoaderNow, 1200);
        })();
    </script>
</body>
</html>
