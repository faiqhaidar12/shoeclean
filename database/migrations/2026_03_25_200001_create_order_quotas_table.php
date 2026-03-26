<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('quota_total')->default(500);
            $table->integer('quota_used')->default(0);
            $table->string('mayar_transaction_id')->nullable();
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index('mayar_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_quotas');
    }
};
