<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Performance indexes for large dataset optimization.
     */
    public function up(): void
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['outlet_id', 'created_at'], 'orders_outlet_created_idx');
            $table->index(['outlet_id', 'status'], 'orders_outlet_status_idx');
            $table->index('status', 'orders_status_idx');
        });

        // Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'payments_status_created_idx');
        });

        // Expenses table indexes
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['outlet_id', 'expense_date'], 'expenses_outlet_date_idx');
        });

        // Customers table indexes
        Schema::table('customers', function (Blueprint $table) {
            $table->index('outlet_id', 'customers_outlet_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_outlet_created_idx');
            $table->dropIndex('orders_outlet_status_idx');
            $table->dropIndex('orders_status_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_created_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_outlet_date_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_outlet_idx');
        });
    }
};
