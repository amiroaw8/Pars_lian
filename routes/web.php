<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\SMSManagementController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication Routes (brute-force protection: 10 requests/minute per IP)
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Password Reset Routes
Route::middleware(['guest'])->group(function() {
    Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
    Route::post('password/send-code', [\App\Http\Controllers\Auth\ForgotPasswordController::class,'sendResetCode'])->name('password.send-code');
    Route::get('password/verify-code', [\App\Http\Controllers\Auth\ForgotPasswordController::class,'showVerifyCodeForm'])->name('password.verify-code');
    Route::post('password/verify-code', [\App\Http\Controllers\Auth\ForgotPasswordController::class,'verifyCode']);
    Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class,'reset'])->name('password.update');
});

// Public attachment download for authenticated owners/customers
Route::middleware(['auth','security.headers'])->group(function() {
    Route::get('/attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])
        ->name('attachments.public.download');
});

Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// 2FA Routes
Route::middleware(['auth', 'security.headers'])->group(function () {
    Route::get('/verify', [TwoFactorController::class, 'index'])->name('verify.index');

    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/verify', [TwoFactorController::class, 'store'])->name('verify.store');
        Route::get('/verify/resend', [TwoFactorController::class, 'resend'])->name('verify.resend');
    });
});

Route::middleware(['auth', 'security.headers'])->group(function () {
    Route::get('/session/limit', [\App\Http\Controllers\Auth\SessionController::class, 'index'])->name('auth.sessions.limit');
    Route::delete('/session/limit/{id}', [\App\Http\Controllers\Auth\SessionController::class, 'destroy'])->name('auth.sessions.destroy');
});

// Central Dashboard Redirect for authenticated users
Route::middleware(['auth', 'session.limit', 'security.headers', 'throttle:web'])->get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('auth.dashboard');

// Dashboard Notifications API
Route::middleware(['auth', 'session.limit', 'throttle:web'])->get('/api/notifications/summary', [App\Http\Controllers\NotificationController::class, 'getSummary'])->name('api.notifications.summary');

// Customer Dashboard Routes
Route::middleware(['auth', 'session.limit', 'security.headers', 'customer.account', 'throttle:web'])->prefix('my-account')->name('customer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [App\Http\Controllers\CustomerDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/repair/{serviceOrder}', [App\Http\Controllers\CustomerDashboardController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/shop/{order}', [App\Http\Controllers\CustomerDashboardController::class, 'showShopOrder'])->name('orders.shop-show');
    Route::get('/invoices', [App\Http\Controllers\CustomerDashboardController::class, 'invoices'])->name('invoices');
    Route::get('/profile', [App\Http\Controllers\CustomerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/tracking', [ShopController::class, 'tracking'])->name('tracking.index');
Route::get('/about', [ShopController::class, 'about'])->name('shop.about');
Route::get('/contact', [ShopController::class, 'contact'])->name('shop.contact');

// New Catalog and SEO Routes
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/product/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

// E-commerce Shop Routes (Keeping legacy for now, but redirects can be added later)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/search', [ShopController::class, 'search'])->name('search');
    Route::get('/category/{category}', [ShopController::class, 'category'])->name('category');
    Route::get('/product/{product}', [ShopController::class, 'show'])->name('show');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product:slug}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{product:slug}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{product:slug}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
    Route::get('/mini-cart', [CartController::class, 'miniCart'])->name('mini');
});

// Checkout Routes
Route::middleware(['auth', 'session.limit', 'security.headers', 'throttle:checkout'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});
Route::middleware(['auth', 'session.limit', 'security.headers', 'throttle:web'])->group(function () {
    Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Payment Routes
Route::middleware(['auth', 'session.limit', 'security.headers', 'throttle:web'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/pay/{order}', [PaymentController::class, 'pay'])->name('pay');
    Route::get('/callback', [PaymentController::class, 'callback'])->name('callback');
});




// API Routes برای device types - عمومی باقی می‌ماند اما با محدودیت درخواست
Route::middleware('throttle:api')->prefix('api/device-types')->group(function () {
    Route::get('children/{name}', [\App\Http\Controllers\API\DeviceTypeController::class, 'children']);
    Route::get('variants/{modelName}', [\App\Http\Controllers\API\DeviceTypeController::class, 'variants']);
});
