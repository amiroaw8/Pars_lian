<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'throttle:api'])->get('/user', function (Request $request) {
    return $request->user();
});

// Device Types API
Route::get('device-types/children/{name}', [\App\Http\Controllers\API\DeviceTypeController::class, 'children']);
Route::get('device-types/variants/{name}', [\App\Http\Controllers\API\DeviceTypeController::class, 'variants']);

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    // Public routes
    Route::get('products', [\App\Http\Controllers\API\ProductAPIController::class, 'index']);
    Route::get('products/{product}', [\App\Http\Controllers\API\ProductAPIController::class, 'show']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'security.headers', 'role:admin,super_admin'])->group(function () {
        Route::post('products', [\App\Http\Controllers\API\ProductAPIController::class, 'store']);
        Route::put('products/{product}', [\App\Http\Controllers\API\ProductAPIController::class, 'update']);
        Route::patch('products/{product}', [\App\Http\Controllers\API\ProductAPIController::class, 'update']);
        Route::delete('products/{product}', [\App\Http\Controllers\API\ProductAPIController::class, 'destroy']);
    });
});
