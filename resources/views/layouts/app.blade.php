<!DOCTYPE html>
<html dir="rtl" lang="fa" data-min-desktop="{{ (int) config('app.desktop_min_width', 1280) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" id="viewport-meta">
    <link rel="canonical" href="{{ url()->current() }}" />
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Organization",
      "name": "فروشگاه پارس لیان",
      "url": "https://amirwebtest1.ir",
      "logo": "https://amirwebtest1.ir/logo.png",
      "description": "فروشگاه تخصصی قطعات کامپیوتر و خدمات تعمیرات",
      "contactPoint": {
        "@@type": "ContactPoint",
        "telephone": "+98-XXX-XXXXXXX",
        "contactType": "sales"
      },
      "sameAs": [
        "https://www.facebook.com/yourpage",
        "https://www.instagram.com/yourpage"
      ]
    }
    </script>
    @stack('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="فروشگاه پارس لیان با ارائه قطعات اورجینال کامپیوتر، خدمات تعمیرات تخصصی و پشتیبانی حرفه‌ای، بهترین انتخاب برای خرید مطمئن و سریع شماست.">
    <meta property="og:description" content="فروشگاه پارس لیان با ارائه قطعات اورجینال کامپیوتر، خدمات تعمیرات تخصصی و پشتیبانی حرفه‌ای، بهترین انتخاب برای خرید مطمئن و سریع شماست.">
    @auth
    <meta name="money-words-url" content="{{ route('automation.money.words') }}">
    @endauth
    <title>@yield('title', 'پارس لیان - سیستم مدیریت خدمات')</title>

    <!-- Fonts & Icons -->
    <link rel="preload" href="{{ asset('fonts/vazirmatn/Vazirmatn-font-face.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fonts/vazirmatn/Vazirmatn-font-face.css') }}"></noscript>
    @include('partials.tabler-icons-assets')
    @vite(['resources/css/app.css', 'resources/css/partials/form-enhancements.css', 'resources/js/app.js'])

    <!-- Logo Preloads -->
    @if(\App\Support\BrandLogo::exists())
        @php
            $logoMobileUrl = asset('images/pars-lian-logo-mobile.webp') . '?v=' . (file_exists(public_path('images/pars-lian-logo-mobile.webp')) ? filemtime(public_path('images/pars-lian-logo-mobile.webp')) : '');
            $logoDesktopUrl = \App\Support\BrandLogo::url();
        @endphp
        <link rel="preload" as="image" href="{{ $logoDesktopUrl }}" imagesrcset="{{ $logoMobileUrl }} 639w, {{ $logoDesktopUrl }} 1000w" imagesizes="(max-width: 639px) 150px, 320px" fetchpriority="high">
    @endif

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @if(\App\Support\BrandLogo::exists())
    <link rel="icon" type="image/png" href="{{ \App\Support\BrandLogo::url() }}">
    <link rel="apple-touch-icon" href="{{ \App\Support\BrandLogo::url() }}">
    @endif

    @yield('styles')
    @yield('css')
    <style>
    :root {
        --header-height: 80px;
        --sidebar-width: 280px;
    }

    body {
        font-family: 'Vazirmatn', Tahoma, sans-serif;
        direction: rtl;
        background-color: #f8fafc;
        min-height: 100vh;
        margin: 0;
    }

    .app-layout {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .top-header {
        height: var(--header-height);
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        padding: 0 2rem;
    }

    .header-container {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    .brand-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 800;
        background: linear-gradient(to left, #fff, #94a3b8);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        white-space: nowrap;
        flex-shrink: 0;
        text-decoration: none;
    }

    .admin-sidebar {
        width: var(--sidebar-width);
        background: #1e293b;
        border-left: 1px solid rgba(255,255,255,0.1);
        overflow-y: scroll;
        scrollbar-gutter: stable;
        display: flex;
        flex-direction: column;
        transition: right 0.3s ease;
        z-index: 1100;
        position: fixed;
        right: calc(var(--sidebar-width) * -1);
        top: var(--header-height);
        bottom: 0;
    }

    .admin-sidebar.open { right: 0; }

    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 1050;
        display: none;
    }

    .sidebar-overlay.show { display: block; }

    .sidebar-menu {
        padding: 1.5rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-height: 100%;
    }

    .menu-group { margin-bottom: 1.5rem; }

    .group-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.55);
        padding: 0 0.75rem 0.4rem;
        display: block;
    }

    .sidebar-search { margin-bottom: 1rem; }

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

    .sidebar-search-input::placeholder { color: rgba(255, 255, 255, 0.45); }

    .sidebar-search-input:focus {
        border-color: rgba(59, 130, 246, 0.6);
        background: rgba(255, 255, 255, 0.12);
    }

    .sidebar-search-wrap { position: relative; }

    .sidebar-search-wrap i {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.45);
        pointer-events: none;
    }

    .admin-sidebar .nav-link-modern {
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

    .admin-sidebar .nav-link-modern:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .admin-sidebar .nav-link-modern.active {
        background: #3b82f6;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .admin-sidebar .nav-link-modern i { font-size: 1.25rem; }

    .user-info-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.4rem 0.8rem;
        background: rgba(255,255,255,0.03);
        border-radius: 1rem;
        border: 1px solid rgba(255,255,255,0.05);
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        color: white;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .user-text {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 700;
        font-size: 0.8rem;
        color: #f8fafc;
    }

    .user-role {
        font-size: 0.65rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .main-wrapper {
        flex: 1;
        width: 100%;
    }

    .content-area {
        padding: 2rem;
        max-width: 1600px;
        margin: 0 auto;
    }

    .nav-toggle-btn {
        display: flex;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        color: white;
        border: 1px solid rgba(255,255,255,0.1);
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .alert-container {
        max-width: 1600px;
        margin: 0 auto 1.5rem;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        animation: fadeIn 0.4s ease-out;
    }

    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }



    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="app-layout">
    @yield('loading')
    <script>
        (function () {
            function forceHideLoader() {
                var loader = document.getElementById('pageLoader');
                if (!loader) return;
                loader.classList.add('hide');
                setTimeout(function() { loader.style.display = 'none'; }, 600);
            }
            // Hide as soon as page is interactive, not after 900ms extra delay
            window.addEventListener('load', forceHideLoader);
            setTimeout(forceHideLoader, 2500);
        })();
    </script>
    @php
        $user = \Illuminate\Support\Facades\Auth::user();
        $isStaff = $user && $user->isEmployee();
        $isShopRoute = request()->is('shop*') ||
                       request()->is('catalog*') ||
                       request()->is('product*') ||
                       request()->is('/') ||
                       request()->is('tracking*') ||
                       request()->is('cart*') ||
                       request()->is('checkout*') ||
                       request()->routeIs('home') ||
                       request()->routeIs('shop.*') ||
                       request()->routeIs('catalog.*') ||
                       request()->routeIs('login') ||
                       request()->routeIs('register');
    @endphp

    @if(!$isShopRoute)
    <header class="top-header">
        <div class="header-container">
            <div class="flex items-center gap-3">
                @auth
                <button type="button" class="nav-toggle-btn" id="navToggle" aria-label="باز کردن منو">
                    <i class="ti ti-menu-2"></i>
                </button>
                @endauth
                <a href="{{ route('home') }}" class="brand-logo">
                    <i class="ti ti-device-laptop text-3xl"></i>
                    <span class="hidden sm:inline">پارس لیان</span>
                </a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <div class="user-info-badge">
                        <div class="avatar-circle">
                            @php
                                $initials = '';
                                $nameParts = explode(' ', $user->name);
                                if (count($nameParts) >= 1) {
                                    $initials = mb_substr($nameParts[0], 0, 1);
                                    if (count($nameParts) >= 2) {
                                        $initials .= mb_substr($nameParts[1], 0, 1);
                                    }
                                }
                            @endphp
                            {{ $initials ?: 'U' }}
                        </div>
                        <div class="user-text hidden sm:flex">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-role">{{ $user->getRoleDisplayName() }}</div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="hidden lg:block" onsubmit="return confirm('آیا از خروج مطمئن هستید؟')">
                        @csrf
                        <button type="submit" class="btn-modern w-full py-4 px-8 bg-amber-600 hover:bg-amber-700 text-white-400 rounded-xl transition-all" title="خروج از سیستم">
                            <i class="ti ti-logout-2 text-xl"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2">
                        <i class="ti ti-login"></i>
                        <span>ورود</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @auth
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="admin-sidebar" id="adminSidebar" aria-label="منوی پنل">
        @include('layouts.partials.admin-sidebar-menu')
    </aside>
    @endauth
    @endif

    <main class="main-wrapper">
        <div class="content-area">
            @if(!$isShopRoute)
                <x-welcome-banner />
            @endif

            @if((!$isShopRoute) && (session('success') || session('error') || $errors->any()))
            <div class="alert-container">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="ti ti-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="ti ti-alert-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="ti ti-alert-triangle"></i>
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            @endif

            <div class="animate-fade-in">
                @yield('content')
            </div>
        </div>
    </main>

    @if(!$isShopRoute)
    <footer class="py-8 text-center text-slate-600 text-xs border-t border-slate-100 mt-auto">
        <div class="container mx-auto">
            <p>© {{ date('Y') }} پارس لیان - سیستم مدیریت خدمات فنی</p>
        </div>
    </footer>
    @endif

    <script>
        function hidePageLoader() {
            const loader = document.getElementById('pageLoader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('hide');
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 600);
                }, 800);
            }
        }

        window.addEventListener('load', hidePageLoader);
        setTimeout(hidePageLoader, 3000);

        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('navToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function closeSidebar() {
                adminSidebar?.classList.remove('open');
                sidebarOverlay?.classList.remove('show');
                document.body.style.overflow = '';
                const toggleIcon = navToggle?.querySelector('i');
                if (toggleIcon) {
                    toggleIcon.classList.remove('ti-x');
                    toggleIcon.classList.add('ti-menu-2');
                }
            }

            if (navToggle && adminSidebar) {
                const toggleIcon = navToggle.querySelector('i');

                navToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willOpen = !adminSidebar.classList.contains('open');
                    adminSidebar.classList.toggle('open', willOpen);
                    sidebarOverlay?.classList.toggle('show', willOpen);
                    document.body.style.overflow = willOpen ? 'hidden' : '';

                    if (toggleIcon) {
                        toggleIcon.classList.toggle('ti-menu-2', !willOpen);
                        toggleIcon.classList.toggle('ti-x', willOpen);
                    }
                });

                sidebarOverlay?.addEventListener('click', closeSidebar);

                adminSidebar.querySelectorAll('a.nav-link-modern').forEach((link) => {
                    link.addEventListener('click', closeSidebar);
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
        });

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    @hasSection('use-alpine')
        <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    @endif

    @yield('scripts')
    @stack('scripts')
</body>
</html>
