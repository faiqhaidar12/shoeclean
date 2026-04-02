<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_gateway')->nullable()->after('status');
            $table->string('gateway_transaction_id')->nullable()->after('mayar_member_id');
            $table->string('gateway_reference')->nullable()->after('gateway_transaction_id');

            $table->index('payment_gateway');
            $table->index('gateway_transaction_id');
        });

        Schema::table('order_quotas', function (Blueprint $table) {
            $table->string('payment_gateway')->nullable()->after('quota_used');
            $table->string('gateway_transaction_id')->nullable()->after('mayar_transaction_id');
            $table->string('gateway_reference')->nullable()->after('gateway_transaction_id');

            $table->index('payment_gateway');
            $table->index('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['payment_gateway']);
            $table->dropIndex(['gateway_transaction_id']);
            $table->dropColumn(['payment_gateway', 'gateway_transaction_id', 'gateway_reference']);
        });

        Schema::table('order_quotas', function (Blueprint $table) {
            $table->dropIndex(['payment_gateway']);
            $table->dropIndex(['gateway_transaction_id']);
            $table->dropColumn(['payment_gateway', 'gateway_transaction_id', 'gateway_reference']);
        });
    }
};
