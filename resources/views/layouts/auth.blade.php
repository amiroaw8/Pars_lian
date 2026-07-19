<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پارس لیان - سیستم مدیریت خدمات')</title>

    <!-- Fonts & Icons -->
    <link href="{{ asset('fonts/vazirmatn/Vazirmatn-font-face.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/partials/form-enhancements.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .auth-wrapper {
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .auth-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            perspective: 1000px;
            mask-image: radial-gradient(circle at center, black, transparent 80%);
        }

        .auth-grid-inner {
            position: absolute;
            inset: 0;
            transform: rotateX(45deg);
            transform-origin: center bottom;
            animation: grid-move 20s linear infinite;
        }

        @keyframes grid-move {
            0% { background-position: 0 0; }
            100% { background-position: 0 500px; }
        }

        .auth-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 1;
        }

        .auth-card-modern {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 480px;
            border-radius: 2.5rem;
            padding: 3.5rem 2.5rem;
            position: relative;
            z-index: 10;
        }

        .auth-logo-modern {
            width: auto;
            height: auto;
            max-width: min(100%, 280px);
            margin: 0 auto 2rem;
            padding: 0;
            background: none;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-logo-modern img {
            display: block;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 72px;
            object-fit: contain;
            border-radius: 1rem;
        }

        .form-control-modern {
            background: rgba(15, 23, 42, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.05);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control-modern:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0F172A inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-label-modern {
            color: #94a3b8;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .btn-modern {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .btn-modern::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .btn-modern:hover::after {
            transform: translateX(100%);
        }

        .btn-modern:active {
            transform: scale(0.98);
        }

        .animate-shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .auth-card-modern {
                padding: 2rem 1.25rem;
                border-radius: 1.5rem;
                margin: 1rem;
            }

            .auth-wrapper {
                padding: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-glow" style="top: -10%; left: -10%;"></div>
        <div class="auth-glow" style="bottom: -10%; right: -10%; background: radial-gradient(circle, rgba(168, 85, 247, 0.15) 0%, transparent 70%);"></div>

        <div class="auth-card-modern animate-fade-in">
            <div class="auth-logo-modern mx-auto flex items-center justify-center">
                <x-brand-logo size="lg" mode="web" class="!h-auto !max-h-[72px] !max-w-[280px] rounded-2xl" />
            </div>
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
