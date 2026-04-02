<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('billable');
            $table->string('gateway');
            $table->string('kind');
            $table->string('plan_key')->nullable();
            $table->string('merchant_order_id')->unique();
            $table->string('reference')->nullable()->index();
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->decimal('fee', 15, 2)->nullable();
            $table->string('status_code')->nullable();
            $table->string('result_code')->nullable();
            $table->string('status_message')->nullable();
            $table->string('product_detail')->nullable();
            $table->string('customer_email')->nullable();
            $table->json('checkout_payload')->nullable();
            $table->json('callback_payload')->nullable();
            $table->json('status_payload')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'kind']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
