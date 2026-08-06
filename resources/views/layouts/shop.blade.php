@extends('layouts.app')

@section('loading')
@endsection

@section('css')
<style>
    /* Shop Specific Styles — CSS variables live in app.css; do not set overflow on :root/html */
    .product-card {
        background: var(--bg-card);
        border-radius: 1.5rem;
        overflow: hidden;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        position: relative;
        box-shadow: var(--shadow-soft);
        color: var(--text-main);
        will-change: transform;
    }

    .product-card:hover {
        transform: translateY(-8px) scale(1.03);
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 1;
        pointer-events: none;
    }

    .product-card:hover::before {
        opacity: 1;
    }

    .product-card::after {
        content: '';
        position: absolute;
        inset: 0;
        padding: 2px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
        border-radius: inherit;
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: xor;
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .product-card:hover::after {
        opacity: 1;
    }

    .product-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover .product-image {
        transform: scale(1.1) rotate(1deg);
    }

    .product-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 1rem 1rem 0 0;
    }

    .product-image-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0) 0%, rgba(139, 92, 246, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 1;
    }

    .product-card:hover .product-image-container::before {
        opacity: 1;
    }

    .price {
        font-size: 1.25rem;
        font-weight: bold;
        color: #059669;
    }

    .sale-price {
        color: #dc2626;
    }

    .original-price {
        text-decoration: line-through;
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .cart-badge {
        position: relative;
    }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc2626;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-secondary:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-outline-product {
        border: 2px solid var(--border-base);
        background: transparent;
        color: var(--text-main);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-outline-product:hover {
        border-color: var(--color-primary-500);
        background: var(--color-primary-500);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }

    /* Enhanced animations and effects */
    .fade-in-stagger {
        opacity: 0;
        animation: fadeInStagger 0.8s ease-out forwards;
    }

    .fade-in-stagger:nth-child(1) { animation-delay: 0.1s; }
    .fade-in-stagger:nth-child(2) { animation-delay: 0.2s; }
    .fade-in-stagger:nth-child(3) { animation-delay: 0.3s; }
    .fade-in-stagger:nth-child(4) { animation-delay: 0.4s; }
    .fade-in-stagger:nth-child(5) { animation-delay: 0.5s; }
    .fade-in-stagger:nth-child(6) { animation-delay: 0.6s; }
    .fade-in-stagger:nth-child(7) { animation-delay: 0.7s; }
    .fade-in-stagger:nth-child(8) { animation-delay: 0.8s; }

    @keyframes fadeInStagger {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Parallax effect for background elements */
    .parallax-element {
        transform: translateZ(0);
        will-change: transform;
    }

    /* Magnetic hover effect */
    .magnetic-hover {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }

    /* Hover lift effect — only composited properties to prevent forced reflow */
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
        will-change: transform;
    }
    .hover-lift--active {
        transform: translateY(-8px) scale(1.02);
    }

    /* Glow effect */
    .glow-effect {
        position: relative;
    }

    .glow-effect::after {
        content: '';
        position: absolute;
        inset: -2px;
        background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
        border-radius: inherit;
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s ease;
    }

    .glow-effect:hover::after {
        opacity: 0.3;
    }

    /* Mobile category drawer — full viewport overlay (not accordion) */
    #mobile-nav {
        z-index: 9990;
    }

    #mobile-nav.hidden {
        display: none !important;
    }

    #mobile-nav-content {
        max-height: 100vh;
        max-height: 100dvh;
    }

    /* Scroll to top button — only animate composited properties */
    a[href="#top"] {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    a[href="#top"].opacity-100 {
        opacity: 1;
        pointer-events: auto;
    }

    /* Page animations — only composited properties */
    .fade-in-up {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform, opacity;
    }

    .fade-in-up.animate {
        opacity: 1;
        transform: translateY(0);
    }

    /* Staggered animation delays */
    .fade-in-up:nth-child(1) { transition-delay: 0.1s; }
    .fade-in-up:nth-child(2) { transition-delay: 0.2s; }
    .fade-in-up:nth-child(3) { transition-delay: 0.3s; }
    .fade-in-up:nth-child(4) { transition-delay: 0.4s; }
    .fade-in-up:nth-child(5) { transition-delay: 0.5s; }
    .fade-in-up:nth-child(6) { transition-delay: 0.6s; }

    /* Enhanced product cards */
    .product-card-enhanced {
        background: var(--bg-card);
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        color: var(--text-main);
    }

    .product-card-enhanced:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .product-card-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card-enhanced:hover::before {
        opacity: 1;
    }

    /* Loading animations */
    .skeleton {
        background: linear-gradient(90deg, var(--border-light) 25%, var(--border-base) 50%, var(--border-light) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Pulse animation for buttons */
    .pulse-button {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    /* Smooth page transitions */
    * {
        scroll-behavior: smooth;
    }

    /* Toast animations */
    @keyframes bounceInRight {
        0% {
            transform: translateX(100%);
            opacity: 0;
        }
        60% {
            transform: translateX(-5%);
            opacity: 1;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        0% {
            transform: translateX(0);
            opacity: 1;
        }
        100% {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes slideOutLeft {
        0% {
            transform: translateX(0);
            opacity: 1;
        }
        100% {
            transform: translateX(-100%);
            opacity: 0;
        }
    }

    @keyframes shrink {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* Notification animations */
    @keyframes slideInRightBounce {
        0% {
            transform: translateX(100%);
            opacity: 0;
        }
        60% {
            transform: translateX(-5%);
            opacity: 1;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Filter animations */
    @keyframes slideDownFadeIn {
        0% {
            transform: translateY(-20px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        0% {
            transform: translateX(0);
            opacity: 1;
        }
        100% {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes progress {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }

    .animate-progress {
        animation: progress linear forwards;
        transform-origin: left;
    }

    /* Magnetic effect enhancements */
    .magnetic-hover {
        cursor: pointer;
    }

    /* Enhanced scrollbar for modern look */
    ::-webkit-scrollbar {
        width: 12px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 6px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.6), rgba(139, 92, 246, 0.6), rgba(236, 72, 153, 0.6));
        border-radius: 6px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, rgba(59, 130, 246, 0.8), rgba(139, 92, 246, 0.8), rgba(236, 72, 153, 0.8));
        transform: scale(1.1);
    }

    /* Advanced Theme System */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);

        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-heavy: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Theme-aware components */
    .theme-aware {
        transition: all 0.3s ease;
    }

    /* Advanced animations */
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .animate-glow {
        animation: glow 2s ease-in-out infinite alternate;
    }

    .animate-shimmer {
        position: relative;
        overflow: hidden;
    }

    .animate-shimmer::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.4),
            transparent
        );
        animation: shimmer 2s infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(5deg); }
    }

    @keyframes glow {
        from { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5); }
        to { box-shadow: 0 0 20px rgba(59, 130, 246, 0.8), 0 0 30px rgba(139, 92, 246, 0.6); }
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* Interactive elements */
    .interactive-card {
        position: relative;
        overflow: hidden;
    }

    .interactive-card::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
        transition: width 0.6s, height 0.6s;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        z-index: 0;
    }

    .interactive-card:hover::before {
        width: 300px;
        height: 300px;
    }

    .interactive-card > * {
        position: relative;
        z-index: 1;
    }

    /* Advanced button styles */
    .btn-3d {
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.2s ease;
    }

    .btn-3d::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        border-radius: inherit;
        transform: translateZ(-1px);
        filter: brightness(0.9);
    }

    .btn-3d:hover {
        transform: translateY(-2px) rotateX(5deg);
    }

    .btn-3d:hover::before {
        transform: translateZ(-2px);
    }

    /* Particle system */
    .particles-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
    }

    .particle {
        position: absolute;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 50%;
        animation: particleFloat 10s linear infinite;
    }

    .particle:nth-child(odd) {
        background: rgba(139, 92, 246, 0.1);
        animation-duration: 8s;
    }

    .particle:nth-child(3n) {
        background: rgba(236, 72, 153, 0.1);
        animation-duration: 12s;
    }

    @keyframes particleFloat {
        0% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) rotate(360deg);
            opacity: 0;
        }
    }

    /* Advanced form styles */
    .form-floating {
        position: relative;
    }

    .form-floating input,
    .form-floating textarea {
        transition: all 0.3s ease;
    }

    .form-floating input:focus,
    .form-floating textarea:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
    }

    .form-floating label {
        position: absolute;
        top: 0;
        right: 1rem;
        padding: 0.75rem;
        pointer-events: none;
        transition: all 0.3s ease;
        color: rgba(107, 114, 128, 0.8);
    }

    .form-floating input:focus + label,
    .form-floating textarea:focus + label,
    .form-floating input:not(:placeholder-shown) + label,
    .form-floating textarea:not(:placeholder-shown) + label {
        top: -0.5rem;
        right: 0.5rem;
        font-size: 0.75rem;
        color: rgb(59, 130, 246);
        background: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    /* Reduced motion for accessibility */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #3b82f6, #8b5cf6);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #2563eb, #7c3aed);
    }

    /* Scrollbar styling for navigation */
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 2px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        border-radius: 2px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, #2563eb, #7c3aed);
    }

    /* Navigation shrink effect on scroll */
    nav.nav-scrolled {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        backdrop-filter: blur(16px);
    }

    /* ═══════════════════════════════════════════════
       Header Auth Buttons — reliable responsive CSS
       (not relying on Tailwind JIT for these)
       ═══════════════════════════════════════════════ */

    /* Desktop: show text buttons, hide icon */
    .header-auth-desktop {
        display: none;
        align-items: center;
        gap: 0.5rem;
        margin-right: 0.25rem;
    }

    .header-auth-mobile {
        display: flex;
    }

    @media (min-width: 640px) {
        .header-auth-desktop {
            display: flex;
        }

        .header-auth-mobile {
            display: none;
        }
    }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .shop-layout {
        overflow-x: clip;
    }

    html {
        direction: ltr;
    }

    html, body {
        overflow-y: scroll !important;
        scrollbar-gutter: stable;
        min-height: 100vh;
    }

    body {
        direction: rtl !important;
    }
</style>
@endsection

@section('content')
@yield('loading')

<div class="shop-layout relative min-h-screen">
    <!-- Advanced Background Effects (Hidden on mobile to save CPU and reduce main-thread rendering overhead) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0 hidden lg:block">
        <!-- Dynamic particle system -->
        <div class="particles-container">
            <!-- Particles will be generated by JavaScript -->
        </div>

        <!-- Geometric shapes -->
        <div class="absolute top-20 left-10 w-4 h-4 border border-blue-400/30 rounded-full animate-float"></div>
        <div class="absolute top-40 right-20 w-6 h-6 border border-purple-400/20 rounded-lg rotate-45 animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute top-60 left-1/4 w-3 h-3 border border-pink-400/30 rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-80 right-1/3 w-5 h-5 border border-green-400/20 rounded-lg animate-float" style="animation-delay: 0.5s;"></div>
        <div class="absolute bottom-40 left-1/2 w-4 h-4 border border-yellow-400/30 rounded-full animate-float" style="animation-delay: 1.5s;"></div>
        <div class="absolute bottom-20 right-10 w-3 h-3 border border-indigo-400/20 rounded-full animate-float" style="animation-delay: 2.5s;"></div>

        <!-- Floating dots -->
        <div class="absolute top-32 right-1/4 w-2 h-2 bg-blue-300/40 rounded-full animate-pulse"></div>
        <div class="absolute bottom-32 left-1/3 w-1 h-1 bg-purple-300/40 rounded-full animate-pulse" style="animation-delay: 0.7s;"></div>
        <div class="absolute top-1/2 left-20 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse" style="animation-delay: 1.3s;"></div>
        <div class="absolute bottom-1/2 right-20 w-1 h-1 bg-green-300/40 rounded-full animate-pulse" style="animation-delay: 1.9s;"></div>
        <div class="absolute top-3/4 left-1/3 w-1 h-1 bg-yellow-300/40 rounded-full animate-pulse" style="animation-delay: 2.1s;"></div>

        <!-- Gradient overlays -->
        <div class="absolute top-0 left-0 w-full h-40 bg-gradient-to-b from-white/10 via-white/5 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full h-40 bg-gradient-to-t from-white/10 via-white/5 to-transparent"></div>

        <!-- Radial gradients -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-400/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-400/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-pink-400/5 rounded-full blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
    </div>
    @if(session('success') && !request()->routeIs('checkout.success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold px-4 py-3 flex items-center gap-2">
                <i class="ti ti-check-circle text-lg"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold px-4 py-3 flex items-center gap-2">
                <i class="ti ti-alert-circle text-lg"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Shop Header & Navigation -->
    <header class="sticky top-0 z-50" id="main-shop-header">
        <h1 class="sr-only">فروشگاه پارس لیان</h1>
        <!-- Unified Header: Logo, Search, Nav & Actions -->
        <div class="bg-white/85 backdrop-blur-md border-b border-white/30 shadow-sm">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">

                <!-- ═══════════════════════════════════════════════════════════════
                     ROW 1 (Mobile & Desktop): Logo  ←  Action Buttons
                     ═══════════════════════════════════════════════════════════════ -->
                <div class="flex flex-wrap items-center justify-between py-2 sm:py-4 gap-y-2">

                    <!-- Logo Section -->
                    <div class="flex items-center flex-shrink-0">
                        <a href="{{ route('shop.index') }}" class="inline-flex shrink-0 hover:opacity-90 transition-opacity duration-200">
                            <x-shop-logo-mark size="xl" class="h-12 sm:h-16 max-w-[180px] sm:max-w-[320px]" />
                        </a>
                    </div>

                    <!-- ═══════════════════════════════════════════════════════════
                         ROW 2 (Mobile only): Search bar pushed to next line
                         On sm+ it sits between logo & buttons via flex order
                         ═══════════════════════════════════════════════════════════ -->
                    <div class="w-full order-3 sm:order-none sm:flex-1 sm:mx-4 sm:max-w-2xl mt-1 sm:mt-0">
                        <form action="{{ route('catalog.index') }}" method="GET" class="relative w-full" role="search" aria-label="جستجوی محصولات">
                            <label for="search-input" class="sr-only">جستجو در محصولات</label>
                            <div class="relative w-full flex items-center">
                                <input type="text" name="q" placeholder="جستجو در محصولات، برندها و..."
                                       class="w-full pr-16 pl-4 py-3 bg-gray-50/80 border border-gray-200 rounded-2xl text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 text-sm font-medium shadow-[inset_0_2px_4px_rgba(0,0,0,0.01)] min-h-[48px]"
                                       value="{{ request('q') }}"
                                       id="search-input"
                                       aria-label="جستجو در محصولات"
                                       title="جستجو در محصولات">
                                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-xl text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-300 p-2" aria-label="جستجو">
                                    <i class="ti ti-search text-xl"></i>
                                </button>
                            </div>

                            <!-- Search Suggestions -->
                            <div class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 mt-2 hidden z-50 max-h-80 overflow-y-auto" id="search-suggestions">
                                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm text-gray-600">
                                        <i class="ti ti-trending-up"></i>
                                        <span>جستجوهای محبوب</span>
                                    </div>
                                </div>
                                <div id="suggestions-list" class="py-2"></div>
                            </div>
                        </form>
                    </div>

                    <!-- User Actions Section (always stays in row 1, left side on mobile) -->
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0 order-2 sm:order-none">
                        <!-- Compare -->
                        <button onclick="showCompareModal()"
                                class="w-11 h-11 bg-purple-50 hover:bg-purple-100 text-purple-600 border border-purple-200/60 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-sm relative group"
                                aria-label="مقایسه محصولات">
                            <i class="ti ti-git-compare text-xl"></i>
                            <span class="compare-count absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold rounded-full w-5 h-5 items-center justify-center border-2 border-white hidden" id="compare-count">0</span>
                        </button>

                        <!-- Wishlist -->
                        <button onclick="showWishlistModal()"
                                class="w-11 h-11 bg-red-50 hover:bg-red-100 text-red-500 border border-red-200/60 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-sm relative group"
                                aria-label="لیست علاقه‌مندی‌ها">
                            <i class="ti ti-heart text-xl"></i>
                            <span class="wishlist-count absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 items-center justify-center border-2 border-white hidden" id="wishlist-count">0</span>
                        </button>

                        <!-- Cart -->
                        <div class="relative group/cart" id="mini-cart-wrapper">
                            <a href="{{ route('cart.index') }}"
                               class="w-11 h-11 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200/60 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-sm relative"
                               aria-label="سبد خرید">
                                <i class="ti ti-shopping-cart text-xl"></i>
                                <span class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 items-center justify-center border-2 border-white animate-bounce hidden" id="cart-count">0</span>
                            </a>

                            <!-- Mini Cart Dropdown -->
                            <div class="absolute top-full left-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden opacity-0 invisible translate-y-2 group-hover/cart:opacity-100 group-hover/cart:visible group-hover/cart:translate-y-0 transition-all duration-300" id="mini-cart-dropdown">
                                <div id="mini-cart-content">
                                    <!-- Content will be loaded via AJAX -->
                                    <div class="p-8 text-center">
                                        <i class="ti ti-loader-2 animate-spin text-3xl text-blue-500 mb-2"></i>
                                        <p class="text-sm text-gray-700">در حال بارگذاری...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            // Provide a lightweight global addToCart for shop pages (accepts slug or product object)
                            if (!window.addToCart) {
                                window.addToCart = async function(slugOrProduct, qty = 1, btn = null) {
                                    try {
                                        const payload = { quantity: qty };
                                        let slug = null;

                                        if (typeof slugOrProduct === 'object') {
                                            slug = slugOrProduct.slug ?? slugOrProduct.id;
                                        } else {
                                            slug = slugOrProduct;
                                        }

                                        // Construct add URL reliably (avoid Blade route parameter encoding issues)
                                        const addUrl = "{{ url('cart/add') }}" + '/' + encodeURIComponent(slug);
                                        const res = await fetch(addUrl, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify(payload)
                                        });

                                        const data = await res.json();
                                        if (data.success) {
                                            fetch("{{ route('cart.mini') }}").then(r => r.text()).then(html => {
                                                document.getElementById('mini-cart-content').innerHTML = html;
                                            }).catch(()=>{});

                                            const miniCount = document.getElementById('cart-count');
                                            if (miniCount && data.cart_count !== undefined) {
                                                miniCount.textContent = data.cart_count;
                                                miniCount.classList.toggle('hidden', data.cart_count <= 0);
                                            } else if (miniCount) {
                                                miniCount.textContent = (parseInt(miniCount.textContent) || 0) + 1;
                                                miniCount.classList.remove('hidden');
                                            }

                                            if (typeof showNotification === 'function') {
                                                showNotification(data.message || 'محصول به سبد خرید اضافه شد.', 'success');
                                            }

                                            if (btn) { btn.classList.add('added'); }
                                            return data;
                                        }

                                        throw new Error(data.message || 'خطا در افزودن به سبد');
                                    } catch (e) {
                                        console.error(e);
                                        alert(e.message || 'خطا در افزودن به سبد خرید');
                                    }
                                }
                            }
                        </script>

                        <!-- User Profile/Login -->
                        @guest
                            <div class="header-auth-desktop">
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors text-sm border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50">
                                    <i class="ti ti-login text-base"></i>
                                    ورود
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-medium hover:from-blue-600 hover:to-purple-600 transition-all duration-300 shadow-md hover:shadow-lg text-sm">
                                    <i class="ti ti-user-plus text-base"></i>
                                    ثبت نام
                                </a>
                            </div>
                            <a href="{{ route('login') }}"
                               class="header-auth-mobile w-10 h-10 bg-blue-500 text-white rounded-xl items-center justify-center shadow-md border border-blue-400"
                               aria-label="ورود به حساب کاربری">
                                <i class="ti ti-user text-lg"></i>
                            </a>
                        @else
                            @php
                                $shopUser = \App\Models\User::query()
                                    ->with('roles')
                                    ->find(auth()->id());
                                $shopUserIsStaff = $shopUser?->isEmployee() ?? false;
                            @endphp
                            <div class="relative mr-1" id="shop-user-menu" data-shop-user-menu data-shop-user-id="{{ $shopUser?->id }}">
                                <button type="button"
                                        data-shop-user-menu-toggle
                                        class="flex items-center gap-1.5 px-2 sm:px-3 py-1.5 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-300"
                                        aria-expanded="false"
                                        aria-controls="shop-user-menu-panel">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                                        <i class="ti ti-user text-white text-sm"></i>
                                    </div>
                                    <div class="hidden sm:flex flex-col items-start leading-tight">
                                        <span class="text-gray-700 font-medium text-sm">{{ $shopUser->name }}</span>
                                        <span class="text-[10px] text-gray-500">{{ $shopUser->getRoleDisplayName() }}</span>
                                    </div>
                                    <i class="ti ti-chevron-down text-gray-400 text-xs transition-transform duration-300" data-shop-user-menu-chevron></i>
                                </button>

                                <div id="shop-user-menu-panel"
                                     data-shop-user-menu-panel
                                     class="hidden absolute left-0 mt-3 w-60 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[60] overflow-hidden">
                                    @php
                                        $roleLabels = [
                                            'super_admin' => 'سوپر ادمین',
                                            'admin' => 'مدیر',
                                            'technician' => 'تعمیرکار',
                                            'receptionist' => 'پذیرش',
                                            'warehouse' => 'انبار',
                                            'accountant' => 'حسابدار',
                                            'customer' => 'مشتری',
                                        ];
                                    @endphp
                                    <div class="px-4 py-3 border-b border-gray-100 bg-slate-50">
                                        <p class="text-xs font-bold text-gray-800 mb-2">{{ $shopUser->name }}</p>
                                        @if($shopUser->roles && $shopUser->roles->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($shopUser->roles as $role)
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-600 font-bold">{{ $roleLabels[$role->name] ?? $role->name }}</span>
                                            @endforeach
                                        </div>
                                        @else
                                        <span class="text-[10px] text-gray-500">مشتری</span>
                                        @endif
                                    </div>
                                    @if($shopUserIsStaff)
                                        <a href="{{ route('auth.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 transition-colors">
                                            <i class="ti ti-layout-dashboard ml-3 text-blue-500"></i>
                                            <span>پنل مدیریت</span>
                                        </a>
                                        <a href="{{ route('automation.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 transition-colors">
                                            <i class="ti ti-tool ml-3 text-indigo-500"></i>
                                            <span>میز کار</span>
                                        </a>
                                    @else
                                        <a href="{{ route('customer.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 transition-colors">
                                            <i class="ti ti-dashboard ml-3 text-blue-500"></i>
                                            <span>پنل کاربری</span>
                                        </a>
                                        <a href="{{ route('customer.orders') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 transition-colors">
                                            <i class="ti ti-package ml-3 text-green-500"></i>
                                            <span>سفارشات من</span>
                                        </a>
                                    @endif
                                    <hr class="my-1 border-gray-100">
                                    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('آیا از خروج مطمئن هستید؟');">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-3 text-rose-600 hover:bg-rose-50 transition-colors">
                                            <i class="ti ti-logout-2 ml-3"></i>
                                            <span>خروج</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════════════
                     ROW 3: Global Navigation — justify-around on mobile, gap on sm+
                     ═══════════════════════════════════════════════════════════════ -->
                <nav class="flex items-center justify-around sm:justify-start sm:gap-6 py-1.5 border-t border-gray-100 transition-[transform,box-shadow,backdrop-filter] duration-300">
                    <!-- Categories Drawer Toggle -->
                    <button onclick="toggleMobileNav()"
                            class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 sm:space-x-2 rtl:sm:space-x-reverse px-2 py-1.5 sm:px-4 sm:py-2 bg-gray-50 hover:bg-gray-100 rounded-xl text-gray-700 transition-all group flex-1 sm:flex-none justify-center">
                        <i class="ti ti-menu-2 text-lg sm:text-base group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] sm:text-sm font-bold">دسته‌بندی‌ها</span>
                    </button>
                    <a href="{{ route('home') }}"
                       class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-1.5 text-gray-600 hover:text-blue-600 transition-colors py-1.5 flex-1 sm:flex-none justify-center">
                        <i class="ti ti-home text-lg sm:text-base"></i>
                        <span class="text-[10px] sm:text-sm font-bold">صفحه اصلی</span>
                    </a>

                    <a href="{{ route('catalog.index') }}"
                       class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-1.5 text-gray-600 hover:text-blue-600 transition-colors py-1.5 flex-1 sm:flex-none justify-center">
                        <i class="ti ti-layout-grid text-lg sm:text-base"></i>
                        <span class="text-[10px] sm:text-sm font-bold">محصولات</span>
                    </a>

                    <a href="{{ route('catalog.index', ['on_sale' => 1]) }}"
                       class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-1.5 text-red-700 hover:text-red-800 transition-colors py-1.5 flex-1 sm:flex-none justify-center">
                        <i class="ti ti-discount text-lg sm:text-base"></i>
                        <span class="text-[10px] sm:text-sm font-bold">تخفیف‌ها</span>
                    </a>

                    <a href="{{ route('tracking.index') }}"
                       class="flex flex-col sm:flex-row items-center gap-0.5 sm:gap-1.5 text-gray-600 hover:text-blue-600 transition-colors py-1.5 flex-1 sm:flex-none justify-center">
                        <i class="ti ti-truck-delivery text-lg sm:text-base"></i>
                        <span class="text-[10px] sm:text-sm font-bold">پیگیری</span>
                    </a>

                    <!-- Desktop-only auth links — handled in header row above -->
                </nav>
            </div>
        </div>



        <!-- Category Side Drawer (all screen sizes) -->
        <div id="mobile-nav" class="fixed inset-0 z-[9990] hidden" aria-hidden="true">
            <div id="mobile-nav-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="toggleMobileNav()"></div>
            <div class="absolute right-0 top-0 bottom-0 w-80 max-w-[90vw] bg-white shadow-2xl transform transition-transform duration-300 translate-x-full flex flex-col min-h-0" id="mobile-nav-content">
                <div class="p-6 flex flex-col flex-1 min-h-0">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-bold text-gray-800">منوی فروشگاه</h2>
                        <button onclick="toggleMobileNav()" class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                            <i class="ti ti-x text-xl"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-4 no-scrollbar">
                        <!-- Mobile Search -->
                        <div class="mb-6">
                            <form action="{{ route('catalog.index') }}" method="GET" class="relative">
                                <input type="text" name="q" placeholder="جستجو در فروشگاه..."
                                       class="w-full py-3 pr-10 pl-4 bg-gray-100 border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500"
                                       value="{{ request('q') }}">
                                <i class="ti ti-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </form>
                        </div>

                        <a href="{{ route('home') }}" class="flex items-center p-4 rounded-2xl {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50 text-gray-700' }} font-bold transition-colors">
                            <i class="ti ti-home ml-3 text-xl"></i>
                            صفحه اصلی
                        </a>

                        <a href="{{ route('catalog.index') }}" class="flex items-center p-4 rounded-2xl {{ request()->routeIs('catalog.*') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50 text-gray-700' }} font-bold transition-colors">
                            <i class="ti ti-layout-grid ml-3 text-xl"></i>
                            همه محصولات
                        </a>

                        <div class="space-y-2" id="mobile-nav-categories">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2 mb-4">دسته‌بندی‌ها</p>
                            @php
                                $mobileCategoryTree = \Cache::remember('shop_category_tree', config('settings.shop_cache_ttl', 3600), function () {
                                    return \App\Models\ProductCategory::query()
                                        ->where('is_active', true)
                                        ->whereNull('parent_id')
                                        ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with(['children' => fn ($q2) => $q2->where('is_active', true)->orderBy('sort_order')])])
                                        ->orderBy('sort_order')
                                        ->orderBy('name')
                                        ->get();
                                });
                            @endphp
                            @forelse($mobileCategoryTree as $category)
                                @include('layouts.partials.mobile-nav-category-node', ['category' => $category, 'depth' => 0])
                            @empty
                                <p class="text-sm text-gray-400 px-4">دسته‌بندی‌ای ثبت نشده است.</p>
                            @endforelse
                        </div>

                        <div class="pt-4 space-y-2">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2 mb-4">خدمات دیگر</p>
                            <a href="{{ route('catalog.index', ['on_sale' => 1]) }}" class="flex items-center p-4 rounded-2xl hover:bg-red-50 text-red-600 transition-colors">
                                <i class="ti ti-discount ml-3 text-xl"></i>
                                جشنواره تخفیف‌ها
                            </a>
                            <a href="{{ route('shop.about') }}" class="flex items-center p-4 rounded-2xl hover:bg-blue-50 text-blue-600 transition-colors">
                                <i class="ti ti-info-circle ml-3 text-xl"></i>
                                درباره ما
                            </a>
                            <a href="{{ route('shop.contact') }}" class="flex items-center p-4 rounded-2xl hover:bg-green-50 text-green-600 transition-colors">
                                <i class="ti ti-phone ml-3 text-xl"></i>
                                تماس با ما
                            </a>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-100">
                        @guest
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('login') }}" class="flex items-center justify-center p-4 rounded-2xl bg-gray-100 text-gray-700 font-bold">ورود</a>
                                <a href="{{ route('register') }}" class="flex items-center justify-center p-4 rounded-2xl bg-blue-600 text-white font-bold shadow-lg">ثبت نام</a>
                            </div>
                        @else
                            <div class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-2xl">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                                    <i class="ti ti-user text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ \Illuminate\Support\Facades\Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">خوش آمدید</p>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </header>



    <!-- Main Content -->
    <main class="flex-1 bg-gradient-to-b from-gray-50/50 via-white to-blue-50/20 relative z-10">
        @yield('shop-content')

        <!-- Content decorative elements -->
        <div class="absolute top-10 right-10 w-20 h-20 border border-blue-200/30 rounded-full opacity-50"></div>
        <div class="absolute bottom-20 left-10 w-16 h-16 border border-purple-200/30 rounded-full opacity-50"></div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-20 h-20 border border-white/20 rounded-full"></div>
            <div class="absolute top-40 right-20 w-32 h-32 border border-white/10 rounded-full"></div>
            <div class="absolute bottom-20 left-1/4 w-16 h-16 border border-white/20 rounded-full"></div>
            <div class="absolute bottom-10 right-10 w-24 h-24 border border-white/10 rounded-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="space-y-4">
                    <div>
                        <x-shop-logo-mark size="lg" />
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        فروشگاه آنلاین قطعات کامپیوتر و لوازم جانبی با بهترین کیفیت، گارانتی معتبر و پشتیبانی ۲۴ ساعته
                    </p>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <a href="#" onclick="return false;" aria-label="اینستاگرام" class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gradient-to-r from-pink-500/20 to-purple-500/20 border border-white/10 hover:border-pink-300/40 hover:bg-pink-500/10 transition-all">
                            <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="ti ti-brand-instagram text-base text-pink-200"></i>
                            </span>
                            <span class="text-xs font-bold text-gray-200">اینستاگرام</span>
                        </a>
                        <a href="#" onclick="return false;" aria-label="تلگرام" class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gradient-to-r from-sky-500/20 to-blue-500/20 border border-white/10 hover:border-sky-300/40 hover:bg-sky-500/10 transition-all">
                            <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="ti ti-brand-telegram text-base text-sky-200"></i>
                            </span>
                            <span class="text-xs font-bold text-gray-200">تلگرام</span>
                        </a>
                        <a href="#" onclick="return false;" aria-label="واتساپ" class="group inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gradient-to-r from-emerald-500/20 to-green-500/20 border border-white/10 hover:border-emerald-300/40 hover:bg-emerald-500/10 transition-all">
                            <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="ti ti-brand-whatsapp text-base text-emerald-200"></i>
                            </span>
                            <span class="text-xs font-bold text-gray-200">واتساپ</span>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white">دسترسی سریع</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('shop.about') }}" class="text-gray-300 hover:text-white transition-colors hover:translate-x-2 inline-flex items-center transform duration-200">
                            <i class="ti ti-chevron-left ml-2 text-xs opacity-60"></i>
                            درباره ما
                        </a></li>
                        <li><a href="{{ route('shop.contact') }}" class="text-gray-300 hover:text-white transition-colors hover:translate-x-2 inline-flex items-center transform duration-200">
                            <i class="ti ti-chevron-left ml-2 text-xs opacity-60"></i>
                            تماس با ما
                        </a></li>
                        <li><a href="{{ route('tracking.index') }}" class="text-gray-300 hover:text-white transition-colors hover:translate-x-2 inline-flex items-center transform duration-200">
                            <i class="ti ti-chevron-left ml-2 text-xs opacity-60"></i>
                            پیگیری سفارش
                        </a></li>
                        <li><a href="{{ route('catalog.index') }}" class="text-gray-300 hover:text-white transition-colors hover:translate-x-2 inline-flex items-center transform duration-200">
                            <i class="ti ti-chevron-left ml-2 text-xs opacity-60"></i>
                            فروشگاه
                        </a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="space-y-4 min-w-0">
                    <h4 class="text-lg font-semibold text-white">دسته‌بندی‌ها</h4>
                    <ul class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                        @foreach(\Cache::remember('shop_categories', config('settings.shop_cache_ttl', 3600), function() { return \App\Models\ProductCategory::active()->parents()->ordered()->get(); }) as $category)
                            <li><a href="{{ route('shop.category', $category) }}" class="text-gray-300 hover:text-white transition-colors hover:translate-x-2 inline-flex items-center transform duration-200">
                                <i class="ti ti-chevron-left ml-2 text-xs opacity-60"></i>
                                {{ $category->name }}
                            </a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white">تماس با ما</h4>
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                            <i class="ti ti-phone ml-3 text-green-400"></i>
                            <span dir="ltr" class="inline-block">{{ \App\Support\CompanyProfile::PHONE }}</span>
                        </div>
                        <div class="flex items-start text-gray-300 hover:text-white transition-colors">
                            <i class="ti ti-map-pin ml-3 text-red-400 mt-0.5"></i>
                            <span class="text-sm leading-relaxed">{{ \App\Support\CompanyProfile::ADDRESS }}</span>
                        </div>
                        <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                            <i class="ti ti-mail ml-3 text-blue-400"></i>
                            <span>info@parslian.ir</span>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $siteLicenses = json_decode(\App\Models\Setting::get('site_licenses', '[]'), true);
                if (!is_array($siteLicenses)) $siteLicenses = [];
            @endphp
            @if(count($siteLicenses) > 0)
            <div class="mt-12 pt-8 border-t border-white/20">
                <h4 class="text-lg font-semibold text-white mb-6 text-center">مجوزهای نماد اعتماد و ساماندهی</h4>
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($siteLicenses as $license)
                        <a href="{{ $license['url'] }}" target="_blank" rel="noopener noreferrer" class="bg-white/10 hover:bg-white/20 border border-white/20 p-3 rounded-2xl transition-all hover:scale-105 inline-block">
                            <img src="{{ asset($license['image']) }}" alt="License" class="h-24 w-auto object-contain drop-shadow-md bg-white rounded-xl p-1">
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Bottom Section -->
            <div class="border-t border-white/20 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-center md:text-right">
                        <p class="text-gray-400 text-sm">
                            © {{ now()->year }} فروشگاه پارس لیان. تمامی حقوق محفوظ است.
                        </p>
                        <a href="#" onclick="return false;" class="inline-block text-gray-100 text-xs mt-1 hover:text-white transition-colors">
                            طراحی و توسعه با ❤️ توسط امیرحسین سبحانی
                        </a>
                    </div>
                    <div class="flex items-center space-x-4 text-xs text-gray-400">
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full ml-2 animate-pulse"></div>
                            آنلاین هستیم
                        </span>
                        <span>پشتیبانی ۲۴ ساعته</span>
                    </div>
                </div>
            </div>
        </div>

    <!-- Floating Action Button -->
    <a href="#top" class="fixed bottom-6 right-6 !w-14 !h-14 !rounded-full aspect-square bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 animate-bounce z-50 group overflow-hidden" onclick="scrollToTop()">
        <i class="ti ti-arrow-up text-xl group-hover:animate-pulse"></i>

        <!-- Tooltip -->
        <div class="absolute bottom-full right-0 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            بازگشت به بالا
            <div class="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-900"></div>
        </div>
    </a>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-6 right-6 z-50 space-y-3"></div>
    </footer>
</div>
@endsection

@section('scripts')
<script>
!function(){function e(e){const t=e.querySelector("[data-shop-user-menu-toggle]"),n=e.querySelector("[data-shop-user-menu-panel]"),o=e.querySelector("[data-shop-user-menu-chevron]");n&&t&&(n.classList.add("hidden"),t.setAttribute("aria-expanded","false"),o&&o.classList.remove("rotate-180"))}function t(){document.querySelectorAll("[data-shop-user-menu]").forEach((function(t){if("1"===t.dataset.shopUserMenuBound)return void e(t);t.dataset.shopUserMenuBound="1";const n=t.querySelector("[data-shop-user-menu-toggle]"),o=t.querySelector("[data-shop-user-menu-panel]"),u=t.querySelector("[data-shop-user-menu-chevron]");n&&o&&(e(t),n.addEventListener("click",(function(e){e.stopPropagation();const t=!o.classList.contains("hidden");o.classList.toggle("hidden",t),n.setAttribute("aria-expanded",t?"false":"true"),u&&u.classList.toggle("rotate-180",!t)})),document.addEventListener("click",(function(n){t.contains(n.target)||e(t)})))}))}"loading"===document.readyState?document.addEventListener("DOMContentLoaded",t):t(),window.addEventListener("pageshow",(function(e){e.persisted&&window.location.reload()}))}();

// Advanced notification system
class NotificationManager {
    constructor() {
        this.container = document.getElementById('notification-container');
        this.notifications = [];
        this.maxNotifications = 5;
    }

    show(message, type = 'info', options = {}) {
        const {
            duration = 5000,
            title = '',
            actions = [],
            persistent = false,
            position = 'top-right'
        } = options;

        // Ensure container exists
        if (!this.container) {
            this.container = document.getElementById('notification-container');
            if (!this.container) {
                const container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'fixed top-6 right-6 z-50 space-y-3';
                document.body.appendChild(container);
                this.container = container;
            }
        }

        const id = Date.now();
        const notification = document.createElement('div');
        notification.className = `notification-item bg-white rounded-2xl shadow-2xl border-r-4 p-4 flex items-start gap-4 transform transition-all duration-500 translate-x-full opacity-0 max-w-sm ${this.getTypeStyles(type)}`;
        notification.dataset.id = id;

        notification.innerHTML = `
            <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center ${this.getIconBg(type)}">
                <i class="ti ${this.getIcon(type)} text-xl"></i>
            </div>
            <div class="flex-1">
                ${title ? `<h4 class="font-bold text-gray-800 text-sm mb-1">${title}</h4>` : ''}
                <p class="text-gray-600 text-xs leading-relaxed">${message}</p>
                ${actions.length ? `
                    <div class="mt-3 flex gap-2">
                        ${actions.map(action => `
                            <button onclick="${action.callback}" class="px-3 py-1 rounded-lg text-[10px] font-bold transition-colors ${action.primary ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">
                                ${action.text}
                            </button>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
            ${!persistent ? `
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ti ti-x text-sm"></i>
                </button>
            ` : ''}
            <div class="absolute bottom-0 left-0 h-1 bg-current opacity-20 animate-progress" style="animation-duration: ${duration}ms"></div>
        `;

        this.container.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        });

        if (!persistent) {
            setTimeout(() => this.hide(id), duration);
        }

        return id;
    }

    hide(id) {
        const notification = this.container.querySelector(`[data-id="${id}"]`);
        if (notification) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 500);
        }
    }

    getTypeStyles(type) {
        switch(type) {
            case 'success': return 'border-green-500 text-green-600';
            case 'error': return 'border-red-500 text-red-600';
            case 'warning': return 'border-yellow-500 text-yellow-600';
            default: return 'border-blue-500 text-blue-600';
        }
    }

    getIconBg(type) {
        switch(type) {
            case 'success': return 'bg-green-50 text-green-600';
            case 'error': return 'bg-red-50 text-red-600';
            case 'warning': return 'bg-yellow-50 text-yellow-600';
            default: return 'bg-blue-50 text-blue-600';
        }
    }

    getIcon(type) {
        switch(type) {
            case 'success': return 'ti-circle-check';
            case 'error': return 'ti-alert-circle';
            case 'warning': return 'ti-alert-triangle';
            default: return 'ti-info-circle';
        }
    }
}

const notificationManager = new NotificationManager();
window.showNotification = (message, type, options) => notificationManager.show(message, type, options);

let mobileNavHost = null;

// Unified Mobile Navigation Toggle
function toggleMobileNav() {
    const mobileNav = document.getElementById('mobile-nav');
    const backdrop = document.getElementById('mobile-nav-backdrop');
    const content = document.getElementById('mobile-nav-content');

    if (!mobileNav) return;

    if (!mobileNavHost && mobileNav.parentElement) {
        mobileNavHost = mobileNav.parentElement;
    }

    function mountMobileNavPortal() {
        if (mobileNav.parentElement !== document.body) {
            document.body.appendChild(mobileNav);
        }
    }

    function restoreMobileNavPortal() {
        if (mobileNavHost && mobileNav.parentElement === document.body) {
            mobileNavHost.appendChild(mobileNav);
        }
    }

    if (mobileNav.classList.contains('hidden')) {
        mountMobileNavPortal();
        mobileNav.classList.remove('hidden');
        mobileNav.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            backdrop.style.opacity = '1';
            content.classList.remove('translate-x-full');
            content.classList.add('translate-x-0');
        });
    } else {
        backdrop.style.opacity = '0';
        content.classList.remove('translate-x-0');
        content.classList.add('translate-x-full');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';

        setTimeout(() => {
            mobileNav.classList.add('hidden');
            mobileNav.setAttribute('aria-hidden', 'true');
            restoreMobileNavPortal();
        }, 400);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const mobileNav = document.getElementById('mobile-nav');
    if (mobileNav?.parentElement) {
        mobileNavHost = mobileNav.parentElement;
    }

    document.querySelectorAll('.mobile-nav-tree-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (e.target.closest('a')) return;
            e.preventDefault();
            const target = document.getElementById(btn.dataset.target);
            const chevron = btn.querySelector('.mobile-nav-chevron');
            if (!target) return;
            const willOpen = target.classList.contains('hidden');
            target.classList.toggle('hidden');
            chevron?.classList.toggle('rotate-180', willOpen);
            btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });

    document.body.addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-add-to-cart');
        if (!btn || btn.disabled) return;
        e.preventDefault();
        const slug = btn.dataset.productSlug;
        if (!slug || typeof window.addToCart !== 'function') return;
        const card = btn.closest('.product-card');
        const qtyInput = card ? card.querySelector('.product-qty') : null;
        const maxQty = parseInt(btn.dataset.maxQty || qtyInput?.dataset?.max || qtyInput?.max || '999', 10);
        let qty = qtyInput ? (parseInt(qtyInput.textContent || qtyInput.value, 10) || 1) : 1;
        qty = Math.min(Math.max(1, qty), maxQty);
        btn.disabled = true;
        try {
            await window.addToCart(slug, qty, btn);
        } finally {
            btn.disabled = false;
        }
    });

    document.body.addEventListener('click', function(e) {
        const minus = e.target.closest('.qty-btn-minus');
        const plus = e.target.closest('.qty-btn-plus');
        if (!minus && !plus) return;
        e.preventDefault();
        e.stopPropagation();
        const box = (minus || plus).closest('[data-qty-control]');
        const input = box?.querySelector('.product-qty');
        if (!input) return;
        const max = parseInt(input.dataset.max || input.max || '999', 10);
        let val = parseInt(input.textContent || input.value, 10) || 1;
        if (plus) val = Math.min(max, val + 1);
        if (minus) val = Math.max(1, val - 1);
        if (input.tagName === 'SPAN') {
            input.textContent = String(val);
        } else {
            input.value = String(val);
        }
    });
});

// Update cart count
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const countElements = document.querySelectorAll('#cart-count, .cart-count');
            countElements.forEach(el => {
                if (data.count > 0) {
                    el.textContent = data.count;
                    el.style.display = 'flex';
                } else {
                    el.textContent = '0';
                    // Don't hide if it's a class-based counter that might be styled differently
                    if (el.id === 'cart-count') {
                        el.style.display = 'none';
                    }
                }
            });
        })
        .catch(error => console.error('Error updating cart count:', error));
}

// Navigation shrink on scroll
// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Animate elements on scroll using IntersectionObserver
function animateOnScroll() {
    const elements = document.querySelectorAll('.fade-in-up:not(.animate)');
    if (!elements.length) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => observer.observe(el));
}

// Magnetic hover effect
function magneticHover() {
    document.querySelectorAll('.magnetic-hover').forEach(element => {
        let rect = null;

        element.addEventListener('mouseenter', () => {
            rect = element.getBoundingClientRect();
        });

        element.addEventListener('mousemove', (e) => {
            if (!rect) {
                rect = element.getBoundingClientRect();
            }
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            element.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translate(0, 0)';
            rect = null;
        });
    });
}

// Placeholder functions for future features
function showCompareModal() {
    showNotification('این بخش در حال توسعه است. بزودی می‌توانید محصولات را با هم مقایسه کنید.', 'info', {
        title: 'مقایسه محصولات ⚖️',
        duration: 5000
    });
}

function showWishlistModal() {
    const items = getWishlistItems();
    const listHtml = items.length === 0
        ? '<p class="text-gray-500 text-sm py-6 text-center">لیست علاقه‌مندی‌ها خالی است.</p>'
        : items.map(function (item) {
            return '<div class="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0">' +
                '<img src="' + item.image + '" alt="" class="w-12 h-12 object-contain rounded-lg bg-gray-50">' +
                '<a href="/product/' + encodeURIComponent(item.slug) + '" class="flex-1 text-sm font-bold text-gray-800 hover:text-blue-600">' + item.name + '</a>' +
                '<button type="button" onclick="toggleWishlist(\'' + item.slug + '\');showWishlistModal();" class="text-rose-500 hover:text-rose-700"><i class="ti ti-trash"></i></button>' +
                '</div>';
        }).join('');

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'علاقه‌مندی‌ها',
            html: '<div class="text-right max-h-80 overflow-y-auto">' + listHtml + '</div>',
            confirmButtonText: 'بستن',
            width: '28rem',
        });
    } else {
        alert(items.length ? items.map(i => i.name).join('\n') : 'لیست خالی است');
    }
}

const WISHLIST_KEY = 'pars_lian_wishlist';

function getWishlistItems() {
    try {
        return JSON.parse(localStorage.getItem(WISHLIST_KEY) || '[]');
    } catch (e) {
        return [];
    }
}

function saveWishlistItems(items) {
    localStorage.setItem(WISHLIST_KEY, JSON.stringify(items));
    updateWishlistCount();
}

function updateWishlistCount() {
    const count = getWishlistItems().length;
    const el = document.getElementById('wishlist-count');
    if (!el) return;
    el.textContent = count;
    el.classList.toggle('hidden', count <= 0);
    el.classList.toggle('flex', count > 0);
}

function toggleWishlist(slug, name, image) {
    let items = getWishlistItems();
    const idx = items.findIndex(i => i.slug === slug);
    if (idx >= 0) {
        items.splice(idx, 1);
        if (typeof showNotification === 'function') {
            showNotification('از علاقه‌مندی‌ها حذف شد.', 'info');
        }
    } else {
        items.push({ slug, name: name || slug, image: image || '' });
        if (typeof showNotification === 'function') {
            showNotification('به علاقه‌مندی‌ها اضافه شد.', 'success');
        }
    }
    saveWishlistItems(items);
    syncWishlistButtons();
}

function syncWishlistButtons() {
    const slugs = getWishlistItems().map(i => i.slug);
    document.querySelectorAll('[data-wishlist-slug]').forEach(function (btn) {
        const slug = btn.dataset.wishlistSlug;
        const icon = btn.querySelector('i');
        const active = slugs.includes(slug);
        btn.classList.toggle('bg-red-500', active);
        btn.classList.toggle('text-white', active);
        btn.classList.toggle('bg-white', !active);
        btn.classList.toggle('text-gray-900', !active);
        if (icon) {
            icon.classList.toggle('ti-heart-filled', active);
            icon.classList.toggle('ti-heart', !active);
        }
    });
}

// Defer wishlist/cart init to idle time to avoid blocking page parse
document.addEventListener('DOMContentLoaded', function () {
    const initWishlist = () => {
        updateWishlistCount();
        syncWishlistButtons();
    };
    if ('requestIdleCallback' in window) {
        requestIdleCallback(initWishlist);
    } else {
        setTimeout(initWishlist, 150);
    }
});



// Particle system
class ParticleSystem {
    constructor() {
        this.container = document.querySelector('.particles-container');
        this.particles = [];
        // Disable particles on mobile/tablet to save CPU
        this.maxParticles = window.innerWidth < 1024 ? 0 : 8;
        this.init();
    }

    init() {
        if (!this.container) return;

        // Gradually create particles to prevent main-thread blockage
        let count = 0;
        const interval = setInterval(() => {
            if (count >= this.maxParticles) {
                clearInterval(interval);
                this.animate();
                return;
            }
            this.createParticle();
            count++;
        }, 100);
    }

    createParticle() {
        const particle = document.createElement('div');
        particle.className = 'particle';

        // Random properties
        const size = Math.random() * 4 + 2;
        const left = Math.random() * 100;
        const animationDuration = Math.random() * 10 + 10;
        const delay = Math.random() * 10;

        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = left + '%';
        particle.style.animationDuration = animationDuration + 's';
        particle.style.animationDelay = delay + 's';

        // Color variations
        const colors = ['rgba(59, 130, 246, 0.1)', 'rgba(139, 92, 246, 0.1)', 'rgba(236, 72, 153, 0.1)', 'rgba(16, 185, 129, 0.1)'];
        particle.style.background = colors[Math.floor(Math.random() * colors.length)];

        this.container.appendChild(particle);
        this.particles.push(particle);

        // Remove particle after animation
        setTimeout(() => {
            particle.remove();
            this.particles = this.particles.filter(p => p !== particle);
            this.createParticle(); // Create new particle
        }, (animationDuration + delay) * 1000);
    }

    animate() {
        // Continuous animation loop
        setInterval(() => {
            if (this.particles.length < this.maxParticles) {
                this.createParticle();
            }
        }, 2000);
    }
}



// Advanced interaction system
class InteractionManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupMagneticEffects();
        this.setupScrollEffects();
        this.setupHoverEffects();
        this.setupClickEffects();
    }

    setupMagneticEffects() {
        document.querySelectorAll('.magnetic-hover').forEach(element => {
            let rect = null;

            element.addEventListener('mouseenter', () => {
                rect = element.getBoundingClientRect();
            });

            element.addEventListener('mousemove', (e) => {
                if (!rect) {
                    rect = element.getBoundingClientRect();
                }
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;
                const deltaX = (e.clientX - centerX) * 0.1;
                const deltaY = (e.clientY - centerY) * 0.1;

                element.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(1.05)`;
            });

            element.addEventListener('mouseleave', () => {
                element.style.transform = 'translate(0, 0) scale(1)';
                rect = null;
            });
        });
    }

    setupScrollEffects() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(element => {
            observer.observe(element);
        });
    }

    setupHoverEffects() {
        // Use CSS class toggling instead of inline styles to avoid forced reflow
        document.querySelectorAll('.hover-lift').forEach(element => {
            element.addEventListener('mouseenter', () => element.classList.add('hover-lift--active'));
            element.addEventListener('mouseleave', () => element.classList.remove('hover-lift--active'));
        });
    }

    setupClickEffects() {
        document.querySelectorAll('.click-ripple').forEach(element => {
            element.addEventListener('click', (e) => {
                const ripple = document.createElement('div');
                // Use relative coords from bounding rect — avoids offsetX/Y forced layout
                const rect = element.getBoundingClientRect();
                const x = e.clientX - rect.left - 10;
                const y = e.clientY - rect.top - 10;
                ripple.style.cssText = `position:absolute;border-radius:50%;background:rgba(255,255,255,0.6);transform:scale(0);animation:ripple 0.6s linear;left:${x}px;top:${y}px;width:20px;height:20px;pointer-events:none`;
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
    }
}

// Advanced analytics and performance monitoring
class AnalyticsManager {
    constructor() {
        this.events = [];
        this.metrics = {};
        this.sessionStart = Date.now();
        this.statsInterval = null;
        this.init();
    }

    init() {
        this.trackPageLoad();
        this.trackUserInteractions();
        this.trackPerformance();
        this.trackEngagement();
    }

    trackPageLoad() {
        window.addEventListener('load', () => {
            const loadTime = performance.now();
            this.metrics.pageLoad = loadTime;

            // Send analytics data
            this.trackEvent('page_load', {
                load_time: loadTime,
                user_agent: navigator.userAgent,
                screen_size: `${screen.width}x${screen.height}`,
                theme: 'light'
            });

            console.log(`🚀 Page loaded in ${loadTime.toFixed(2)}ms`);
        });
    }

    trackUserInteractions() {
        // Track button clicks
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button, a[role="button"], .btn-primary, .btn-secondary');
            if (button) {
                this.trackEvent('button_click', {
                    button_text: button.textContent?.trim(),
                    button_class: button.className,
                    page_url: window.location.href
                });
            }
        });

        // Track form submissions
        document.addEventListener('submit', (e) => {
            this.trackEvent('form_submit', {
                form_action: e.target.action,
                form_method: e.target.method,
                page_url: window.location.href
            });
        });

    }

    trackPerformance() {
        // Monitor Core Web Vitals
        if ('web-vitals' in window) {
            webVitals.getCLS(console.log);
            webVitals.getFID(console.log);
            webVitals.getFCP(console.log);
            webVitals.getLCP(console.log);
            webVitals.getTTFB(console.log);
        }

        // Monitor memory usage
        if ('memory' in performance) {
            setInterval(() => {
                const memInfo = performance.memory;
                this.metrics.memory = {
                    used: memInfo.usedJSHeapSize,
                    total: memInfo.totalJSHeapSize,
                    limit: memInfo.jsHeapSizeLimit
                };
            }, 30000); // Every 30 seconds
        }

        // Network monitoring removed — wrapping window.fetch globally adds overhead to every API call

        // Product recommendation system
        this.productViews = JSON.parse(localStorage.getItem('productViews') || '[]');
        this.categoryViews = JSON.parse(localStorage.getItem('categoryViews') || '{}');
        this.searchHistory = JSON.parse(localStorage.getItem('searchHistory') || '[]');

        // Track product views
        window.trackProductView = (productId, categoryId) => {
            // Add to product views
            if (!this.productViews.includes(productId)) {
                this.productViews.push(productId);
                if (this.productViews.length > 50) {
                    this.productViews.shift(); // Keep last 50
                }
                localStorage.setItem('productViews', JSON.stringify(this.productViews));
            }

            // Track category views
            this.categoryViews[categoryId] = (this.categoryViews[categoryId] || 0) + 1;
            localStorage.setItem('categoryViews', JSON.stringify(this.categoryViews));

            this.trackEvent('product_view', { product_id: productId, category_id: categoryId });
        };

        // Track searches
        window.trackSearch = (query) => {
            if (query && query.trim()) {
                this.searchHistory.push({
                    query: query.trim(),
                    timestamp: Date.now()
                });

                if (this.searchHistory.length > 20) {
                    this.searchHistory.shift(); // Keep last 20
                }

                localStorage.setItem('searchHistory', JSON.stringify(this.searchHistory));
                this.trackEvent('search', { query: query.trim() });
            }
        };

        // Get personalized recommendations
        this.getRecommendations = () => {
            const recommendations = {
                basedOnViews: [],
                basedOnCategory: [],
                basedOnSearches: []
            };

            // Get most viewed categories
            const topCategories = Object.entries(this.categoryViews)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 2)
                .map(([categoryId]) => categoryId);

            // Get recent searches
            const recentSearches = this.searchHistory
                .slice(-5)
                .map(item => item.query);

            return {
                topCategories,
                recentSearches,
                viewedProducts: this.productViews,
                recommendations
            };
        };
    }

    trackEngagement() {
        this.maxScroll = 0;
        this.docHeight = document.documentElement.scrollHeight - window.innerHeight;
        
        window.addEventListener('resize', () => {
            this.docHeight = document.documentElement.scrollHeight - window.innerHeight;
        });

        this.handleScroll = (scrollTop) => {
            const scrollPercent = Math.round((scrollTop / (this.docHeight || 1)) * 100);

            if (scrollPercent > this.maxScroll) {
                this.maxScroll = scrollPercent;
                if (scrollPercent % 25 === 0) { // Track every 25%
                    this.trackEvent('scroll_depth', { percentage: scrollPercent });
                }
            }
        };

        // Track time on page
        let startTime = Date.now();
        window.addEventListener('beforeunload', () => {
            const timeSpent = Date.now() - startTime;
            this.trackEvent('time_on_page', {
                duration: timeSpent,
                page_url: window.location.href
            });
        });

        // Track visibility changes
        document.addEventListener('visibilitychange', () => {
            this.trackEvent('visibility_change', {
                state: document.visibilityState,
                timestamp: Date.now()
            });
        });
    }

    trackEvent(eventName, data = {}) {
        const event = {
            name: eventName,
            data: data,
            timestamp: Date.now(),
            page: window.location.pathname
        };
        this.events.push(event);
        
        // In a real app, you would send this to your backend
        // console.log('📊 Analytics Event:', event);
    }
}

// Centralized Passive Scroll Manager
let isScrolling = false;
let nav = null;
let scrollButton = null;

function handleGlobalScroll() {
    const currentScrollY = window.pageYOffset || window.scrollY;
    
    // 1. Navigation shrink
    if (nav) {
        if (currentScrollY > 50) {
            nav.classList.add('nav-scrolled');
        } else {
            nav.classList.remove('nav-scrolled');
        }
    }
    
    // 2. Scroll to top button
    if (scrollButton) {
        if (currentScrollY > 300) {
            scrollButton.classList.remove('opacity-0', 'pointer-events-none');
            scrollButton.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            scrollButton.classList.remove('opacity-100', 'pointer-events-auto');
            scrollButton.classList.add('opacity-0', 'pointer-events-none');
        }
    }
    

    
    // 4. Analytics scroll depth tracking
    if (window.analyticsManager && typeof window.analyticsManager.handleScroll === 'function') {
        window.analyticsManager.handleScroll(currentScrollY);
    }
    
    isScrolling = false;
}

// Initialize managers using requestIdleCallback to prevent blocking initial load
document.addEventListener('DOMContentLoaded', () => {
    const initManagers = () => {
        window.interactionManager = new InteractionManager();
        window.analyticsManager = new AnalyticsManager();
        window.particleSystem = new ParticleSystem();
        
        // Cache selectors for scroll performance
        nav = document.querySelector('nav');
        scrollButton = document.querySelector('a[href="#top"]');
        
        // Initial triggers
        handleGlobalScroll();
        animateOnScroll();
        
        // Passive scroll event listener running via requestAnimationFrame
        window.addEventListener('scroll', () => {
            if (!isScrolling) {
                window.requestAnimationFrame(handleGlobalScroll);
                isScrolling = true;
            }
        }, { passive: true });
    };

    if ('requestIdleCallback' in window) {
        requestIdleCallback(initManagers);
    } else {
        setTimeout(initManagers, 100);
    }
});

// Helper for filter form
function applyFilters() {
    document.getElementById('filter-form').submit();
}

function clearFilters() {
    const form = document.getElementById('filter-form');
    form.querySelectorAll('input, select').forEach(el => {
        el.value = '';
    });
    form.submit();
}

// Search suggestions logic
const searchInput = document.getElementById('search-input');
const suggestionsBox = document.getElementById('search-suggestions');
const suggestionsList = document.getElementById('suggestions-list');

if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        if (query.length > 2) {
            // Simulated suggestions - in real app, fetch from API
            const suggestions = [
                `محصولات مرتبط با ${query}`,
                `برندهای مرتبط با ${query}`,
                `دسته‌بندی‌های ${query}`
            ];
            
            suggestionsList.innerHTML = suggestions.map(s => `
                <a href="/shop/search?q=${s}" class="block px-6 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                    <i class="ti ti-search ml-3 text-gray-300"></i>
                    ${s}
                </a>
            `).join('');
            
            suggestionsBox.classList.remove('hidden');
        } else {
            suggestionsBox.classList.add('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });
}

// Voice search simulation
function startVoiceSearch() {
    showNotification('سیستم تشخیص صدا در حال آماده‌سازی است...', 'info');
}

// Mini Cart AJAX
document.addEventListener('DOMContentLoaded', function() {
    const miniCartWrapper = document.getElementById('mini-cart-wrapper');
    const miniCartContent = document.getElementById('mini-cart-content');
    let isCartLoaded = false;

    if (miniCartWrapper && miniCartContent) {
        miniCartWrapper.addEventListener('mouseenter', function() {
            if (!isCartLoaded) {
                fetch('{{ route("cart.mini") }}')
                    .then(response => response.text())
                    .then(html => {
                        miniCartContent.innerHTML = html;
                        isCartLoaded = true;
                    })
                    .catch(error => {
                        console.error('Error loading mini cart:', error);
                        miniCartContent.innerHTML = '<div class="p-4 text-center text-red-500">خطا در بارگذاری سبد خرید</div>';
                    });
            }
        });
    }
});

// Mini Cart Remove Item
window.removeFromMiniCart = function(button, slug) {
    // Prevent event bubbling if needed
    if (event) event.stopPropagation();

    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="ti ti-loader animate-spin text-lg"></i>';

    const url = "{{ route('cart.remove', ':slug') }}".replace(':slug', slug);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        if (response.ok) {
            // Refresh mini cart
            const miniCartContent = document.getElementById('mini-cart-content');
            const res = await fetch('{{ route("cart.mini") }}');
            const html = await res.text();
            miniCartContent.innerHTML = html;
            
            // Update counts
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
            
            // Show notification
            if (typeof showNotification === 'function') {
                showNotification('محصول با موفقیت حذف شد', 'success');
            }
        } else {
            throw new Error('Failed to remove item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = originalContent;
        if (typeof showNotification === 'function') {
            showNotification('خطا در حذف محصول', 'error');
        }
    });
};
</script>
@yield('scripts')
@endsection
