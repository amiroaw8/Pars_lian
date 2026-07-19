<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/automation.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckUserActive::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TwoFactorMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\StaffSessionTimeout::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CaptureScrollPosition::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'technician' => \App\Http\Middleware\TechnicianMiddleware::class,
            'profile.complete' => \App\Http\Middleware\EnsureProfileIsComplete::class,
            'session.limit' => \App\Http\Middleware\CheckSessionLimit::class,
            'active' => \App\Http\Middleware\CheckUserActive::class,
            'two-factor' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'customer.account' => \App\Http\Middleware\EnsureCustomerAccount::class,
        ]);

        $middleware->append(\App\Http\Middleware\SanitizeInput::class);
        $middleware->append(\App\Http\Middleware\NormalizeMoneyInputs::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => 'لطفاً ابتدا وارد سیستم شوید.'
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => 'شما اجازه دسترسی به این بخش را ندارید.'
                ], 403);
            }
            if (! \Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('home');
            }
            return response()->view('errors.403', ['message' => 'شما اجازه دسترسی به این بخش را ندارید.'], 403);
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => 'رکورد مورد نظر یافت نشد.'
                ], 404);
            }
            return response()->view('errors.404', ['message' => 'رکورد مورد نظر یافت نشد.'], 404);
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => 'داده‌های ارسالی معتبر نیستند.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $message = config('app.debug') ? $e->getMessage() : 'خطایی در پایگاه داده رخ داده است.';
                return response()->json([
                    'message' => $message
                ], 500);
            }

            if (!config('app.debug')) {
                return response()->view('errors.500', ['message' => 'خطایی در سیستم رخ داده است. لطفاً بعداً تلاش کنید.'], 500);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => 'صفحه یا منبع مورد نظر یافت نشد.'
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->routeIs('logout') || $request->is('logout')) {
                if (Auth::check()) {
                    Auth::logout();
                }
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('home');
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'خطایی رخ داده است.'
                ], $e->getStatusCode());
            }
        });
    })->create();
