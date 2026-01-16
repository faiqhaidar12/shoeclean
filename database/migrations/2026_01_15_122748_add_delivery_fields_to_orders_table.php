<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_type', ['regular', 'pickup', 'delivery'])->default('regular')->after('notes');
            $table->text('pickup_address')->nullable()->after('order_type');
            $table->text('delivery_address')->nullable()->after('pickup_address');
            $table->integer('pickup_fee')->default(0)->after('delivery_address');
            $table->integer('delivery_fee')->default(0)->after('pickup_fee');
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete()->after('delivery_fee');
            $table->integer('discount_amount')->default(0)->after('promo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn(['order_type', 'pickup_address', 'delivery_address', 'pickup_fee', 'delivery_fee', 'promo_id', 'discount_amount']);
        });
    }
};
