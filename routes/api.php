<?php

use App\Http\Controllers\Api\AuthSessionController;
use App\Http\Controllers\Api\CustomerManagementController;
use App\Http\Controllers\Api\DashboardSummaryController;
use App\Http\Controllers\Api\ExpenseManagementController;
use App\Http\Controllers\Api\InternalOrderCreateController;
use App\Http\Controllers\Api\OrderManagementController;
use App\Http\Controllers\Api\OutletManagementController;
use App\Http\Controllers\Api\PromoManagementController;
use App\Http\Controllers\Api\ReportManagementController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\PublicStorefrontController;
use App\Http\Controllers\Api\PublicTrackingController;
use App\Http\Controllers\Api\ServiceManagementController;
use App\Http\Controllers\Api\SubscriptionManagementController;
use App\Http\Controllers\Api\SurveyManagementController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::get('/home', [PublicContentController::class, 'home']);
    Route::get('/pricing', [PublicContentController::class, 'pricing']);
    Route::get('/track', [PublicTrackingController::class, 'show']);
    Route::get('/track/{invoice}', [PublicTrackingController::class, 'show'])
        ->where('invoice', '.*');
    Route::get('/surveys/{survey:slug}', [SurveyManagementController::class, 'publicShow']);
    Route::post('/surveys/{survey:slug}/responses', [SurveyManagementController::class, 'publicStoreResponse']);
    Route::get('/outlets', [PublicStorefrontController::class, 'index']);
    Route::get('/outlets/{slug}', [PublicStorefrontController::class, 'show']);
    Route::post('/promos/validate', [PublicStorefrontController::class, 'validatePromo']);
    Route::post('/orders', [PublicStorefrontController::class, 'store']);
    Route::get('/outlets/{slug}/orders/{order}/success', [PublicStorefrontController::class, 'success']);
});

Route::middleware('web')->group(function () {
    Route::post('/auth/register', [AuthSessionController::class, 'register'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/auth/login', [AuthSessionController::class, 'store'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/auth/logout', [AuthSessionController::class, 'destroy'])
        ->withoutMiddleware([VerifyCsrfToken::class]);

        Route::middleware(['auth'])->group(function () {
            Route::get('/auth/session', [AuthSessionController::class, 'show']);
            Route::get('/dashboard/summary', [DashboardSummaryController::class, 'show']);
            Route::get('/subscription/summary', [SubscriptionManagementController::class, 'show']);
            Route::post('/subscription/checkout/{plan}', [SubscriptionManagementController::class, 'checkout']);
            Route::middleware('role:owner,admin')->group(function () {
                Route::get('/outlets', [OutletManagementController::class, 'index']);
                Route::post('/outlets', [OutletManagementController::class, 'store']);
                Route::get('/outlets/{outlet:id}', [OutletManagementController::class, 'show']);
                Route::put('/outlets/{outlet:id}', [OutletManagementController::class, 'update']);
                Route::get('/reports/summary', [ReportManagementController::class, 'summary']);
                Route::get('/reports/orders/export', [ReportManagementController::class, 'exportOrders']);
                Route::get('/reports/expenses/export', [ReportManagementController::class, 'exportExpenses']);
            });
            Route::middleware('role:owner')->group(function () {
                Route::get('/surveys', [SurveyManagementController::class, 'index']);
                Route::post('/surveys', [SurveyManagementController::class, 'store']);
                Route::get('/surveys/{survey:id}', [SurveyManagementController::class, 'show']);
                Route::patch('/surveys/{survey:id}/toggle', [SurveyManagementController::class, 'toggle']);
                Route::delete('/surveys/{survey:id}', [SurveyManagementController::class, 'destroy']);
            });
            Route::middleware('role:owner,admin,staff')->group(function () {
                Route::get('/team', [UserManagementController::class, 'index']);
            Route::post('/team', [UserManagementController::class, 'store']);
            Route::get('/team/{user}', [UserManagementController::class, 'show']);
            Route::put('/team/{user}', [UserManagementController::class, 'update']);
            Route::get('/services', [ServiceManagementController::class, 'index']);
            Route::post('/services', [ServiceManagementController::class, 'store']);
            Route::get('/services/{service}', [ServiceManagementController::class, 'show']);
            Route::put('/services/{service}', [ServiceManagementController::class, 'update']);
            Route::get('/expenses', [ExpenseManagementController::class, 'index']);
            Route::post('/expenses', [ExpenseManagementController::class, 'store']);
            Route::get('/expenses/{expense}', [ExpenseManagementController::class, 'show']);
            Route::put('/expenses/{expense}', [ExpenseManagementController::class, 'update']);
            Route::get('/promos', [PromoManagementController::class, 'index']);
            Route::post('/promos', [PromoManagementController::class, 'store']);
            Route::get('/promos/{promo}', [PromoManagementController::class, 'show']);
            Route::put('/promos/{promo}', [PromoManagementController::class, 'update']);
            Route::get('/orders/create/meta', [InternalOrderCreateController::class, 'meta']);
            Route::get('/orders/create/customers', [InternalOrderCreateController::class, 'searchCustomers']);
            Route::post('/orders/create/customers', [InternalOrderCreateController::class, 'quickAddCustomer']);
            Route::post('/orders/create/promos/validate', [InternalOrderCreateController::class, 'validatePromo']);
            Route::post('/orders/create', [InternalOrderCreateController::class, 'store']);
            Route::get('/customers', [CustomerManagementController::class, 'index']);
            Route::post('/customers', [CustomerManagementController::class, 'store']);
            Route::get('/customers/{customer}', [CustomerManagementController::class, 'show']);
            Route::put('/customers/{customer}', [CustomerManagementController::class, 'update']);
            Route::get('/orders', [OrderManagementController::class, 'index']);
            Route::get('/orders/{order}', [OrderManagementController::class, 'show']);
            Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus']);
            Route::post('/orders/{order}/mark-paid', [OrderManagementController::class, 'markPaid']);
            Route::post('/orders/{order}/verify-payment', [OrderManagementController::class, 'verifyPayment']);
        });
    });
});
