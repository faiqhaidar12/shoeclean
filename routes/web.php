<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $services = \App\Models\Service::active()->take(4)->get();
    return view('welcome', compact('services'));
});

// Public Tracking (no auth)
Route::get('/track', \App\Livewire\TrackOrder::class)->name('track');
Route::get('/track/{invoice}', \App\Livewire\TrackOrder::class)->where('invoice', '.*')->name('track.invoice');

// Public Storefront Order (no auth)
Route::get('/order', \App\Livewire\SelectOutlet::class)->name('public.order.select');
Route::get('/order/{outlet:slug}', \App\Livewire\PublicOrder::class)->name('public.order');
Route::get('/order/{outlet:slug}/success/{order}', \App\Livewire\OrderSuccess::class)->name('public.order.success');

// Public Survey (no auth)
Route::get('/survey/{survey:slug}', \App\Livewire\FillSurvey::class)->name('survey.fill');

// Mayar Webhook (public, no auth)
Route::post('/webhook/mayar', [\App\Http\Controllers\MayarWebhookController::class, 'handle'])->name('webhook.mayar');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Super Admin
Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/', \App\Livewire\SuperAdminDashboard::class)->name('superadmin.dashboard');
    Route::get('/orders', \App\Livewire\SuperAdmin\ListOrders::class)->name('superadmin.orders.index');
    Route::get('/subscriptions', \App\Livewire\SuperAdmin\SubscriptionInsights::class)->name('superadmin.subscriptions.index');
    Route::get('/surveys', \App\Livewire\Surveys\ListSurveys::class)->name('superadmin.surveys.index');
    Route::get('/surveys/create', \App\Livewire\Surveys\CreateSurvey::class)->name('superadmin.surveys.create');
    Route::get('/surveys/{survey}/results', \App\Livewire\Surveys\SurveyResults::class)->name('superadmin.surveys.results');
    Route::get('/feedbacks', \App\Livewire\SuperAdmin\ListFeedbacks::class)->name('superadmin.feedbacks.index');
    Route::get('/reports/marketing/pdf', [\App\Http\Controllers\ReportController::class, 'marketingKitPdf'])->name('superadmin.reports.marketing.pdf');
});

require __DIR__.'/auth.php';

// Owner Only
Route::middleware(['auth', 'verified', 'role:owner'])->group(function () {
    Route::get('/outlets', \App\Livewire\Outlets\ListOutlets::class)->name('outlets.index');
    Route::get('/outlets/create', \App\Livewire\Outlets\CreateOutlet::class)->name('outlets.create');

    // Subscription
    Route::get('/subscription', \App\Livewire\Subscription\SubscriptionPage::class)->name('subscription');

    // Surveys (Owner)
    Route::get('/surveys', \App\Livewire\Surveys\ListSurveys::class)->name('surveys.index');
    Route::get('/surveys/create', \App\Livewire\Surveys\CreateSurvey::class)->name('surveys.create');
    Route::get('/surveys/{survey}/results', \App\Livewire\Surveys\SurveyResults::class)->name('surveys.results');
});

// Owner + Admin Only
Route::middleware(['auth', 'verified', 'role:owner,admin'])->group(function () {
    Route::get('/outlets/{outlet:slug}/edit', \App\Livewire\Outlets\EditOutlet::class)->name('outlets.edit');

    // Services
    Route::get('/services', \App\Livewire\Services\ListServices::class)->name('services.index');
    Route::get('/services/create', \App\Livewire\Services\CreateService::class)->name('services.create');
    Route::get('/services/{service}/edit', \App\Livewire\Services\EditService::class)->name('services.edit');

    // Users
    Route::get('/users', \App\Livewire\Users\ListUsers::class)->name('users.index');
    Route::get('/users/create', \App\Livewire\Users\CreateUser::class)->name('users.create');
    Route::get('/users/{user}/edit', \App\Livewire\Users\EditUser::class)->name('users.edit');

    // Expenses
    Route::get('/expenses', \App\Livewire\Expenses\ListExpenses::class)->name('expenses.index');
    Route::get('/expenses/create', \App\Livewire\Expenses\CreateExpense::class)->name('expenses.create');
    Route::get('/expenses/{expense}/edit', \App\Livewire\Expenses\EditExpense::class)->name('expenses.edit');

    // Promos
    Route::get('/promos', \App\Livewire\Promos\ListPromos::class)->name('promos.index');
    Route::get('/promos/create', \App\Livewire\Promos\CreatePromo::class)->name('promos.create');

    // Reports Export
    Route::get('/reports/orders/excel', [\App\Http\Controllers\ReportController::class, 'ordersExcel'])->name('reports.orders.excel');
    Route::get('/reports/orders/pdf', [\App\Http\Controllers\ReportController::class, 'ordersPdf'])->name('reports.orders.pdf');
    Route::get('/reports/expenses/excel', [\App\Http\Controllers\ReportController::class, 'expensesExcel'])->name('reports.expenses.excel');
    Route::get('/reports/expenses/pdf', [\App\Http\Controllers\ReportController::class, 'expensesPdf'])->name('reports.expenses.pdf');
});

// Owner + Admin + Staff
Route::middleware(['auth', 'verified', 'role:owner,admin,staff'])->group(function () {
    // Customers
    Route::get('/customers', \App\Livewire\Customers\ListCustomers::class)->name('customers.index');
    Route::get('/customers/create', \App\Livewire\Customers\CreateCustomer::class)->name('customers.create');
    Route::get('/customers/{customer}/edit', \App\Livewire\Customers\EditCustomer::class)->name('customers.edit');

    // Orders
    Route::get('/orders', \App\Livewire\Orders\ListOrders::class)->name('orders.index');
    Route::get('/orders/create', \App\Livewire\Orders\CreateOrder::class)->name('orders.create');
    Route::get('/orders/{order}', \App\Livewire\Orders\ViewOrder::class)->name('orders.view');
    Route::get('/orders/{order}/print', [\App\Http\Controllers\ReportController::class, 'printInvoice'])->name('orders.print');
});
