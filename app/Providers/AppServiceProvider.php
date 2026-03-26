<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Opcodes\LogViewer\Facades\LogViewer::auth(function ($request) {
            return $request->user() && $request->user()->isOwner();
        });

        // View Composer for Sidebar Order Count
        view()->composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $query = \App\Models\Order::whereIn('status', ['pending', 'processing']);

                // Same logic as ListOrders.php for outlet scoping
                if ($user->isOwner()) {
                    if (session('current_outlet_id')) {
                        $query->where('outlet_id', session('current_outlet_id'));
                    } else {
                        $query->whereIn('outlet_id', $user->ownedOutlets->pluck('id'));
                    }
                } else {
                    $query->where('outlet_id', $user->outlet_id);
                }

                $view->with('processingOrdersCount', $query->count());
            }
        });
    }
}
