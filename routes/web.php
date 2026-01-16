<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Public Tracking (no auth)
Route::get('/track', \App\Livewire\TrackOrder::class)->name('track');
Route::get('/track/{invoice}', \App\Livewire\TrackOrder::class)->where('invoice', '.*')->name('track.invoice');

// Midtrans Webhook (public, no auth)
Route::post('/payment/notification', [\App\Http\Controllers\PaymentController::class, 'notification'])->name('payment.notification');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

// Owner Only
Route::middleware(['auth', 'verified', 'role:owner'])->group(function () {
    Route::get('/outlets', \App\Livewire\Outlets\ListOutlets::class)->name('outlets.index');
    Route::get('/outlets/create', \App\Livewire\Outlets\CreateOutlet::class)->name('outlets.create');
    Route::get('/outlets/{outlet}/edit', \App\Livewire\Outlets\EditOutlet::class)->name('outlets.edit');
});

// Owner + Admin Only
Route::middleware(['auth', 'verified', 'role:owner,admin'])->group(function () {
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

