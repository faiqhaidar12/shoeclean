<?php

use App\Http\Controllers\Api\AuthSessionController;
use App\Http\Controllers\Api\DashboardSummaryController;
use App\Http\Controllers\Api\OrderManagementController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\PublicStorefrontController;
use App\Http\Controllers\Api\PublicTrackingController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::get('/home', [PublicContentController::class, 'home']);
    Route::get('/pricing', [PublicContentController::class, 'pricing']);
    Route::get('/track/{invoice}', [PublicTrackingController::class, 'show'])
        ->where('invoice', '.*');
    Route::get('/outlets', [PublicStorefrontController::class, 'index']);
    Route::get('/outlets/{slug}', [PublicStorefrontController::class, 'show']);
    Route::post('/promos/validate', [PublicStorefrontController::class, 'validatePromo']);
    Route::post('/orders', [PublicStorefrontController::class, 'store']);
    Route::get('/outlets/{slug}/orders/{order}/success', [PublicStorefrontController::class, 'success']);
});

Route::middleware('web')->group(function () {
    Route::post('/auth/login', [AuthSessionController::class, 'store'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/auth/logout', [AuthSessionController::class, 'destroy'])
        ->withoutMiddleware([VerifyCsrfToken::class]);

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/auth/session', [AuthSessionController::class, 'show']);
        Route::get('/dashboard/summary', [DashboardSummaryController::class, 'show']);
        Route::middleware('role:owner,admin,staff')->group(function () {
            Route::get('/orders', [OrderManagementController::class, 'index']);
            Route::get('/orders/{order}', [OrderManagementController::class, 'show']);
            Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus']);
            Route::post('/orders/{order}/mark-paid', [OrderManagementController::class, 'markPaid']);
            Route::post('/orders/{order}/verify-payment', [OrderManagementController::class, 'verifyPayment']);
        });
    });
});
