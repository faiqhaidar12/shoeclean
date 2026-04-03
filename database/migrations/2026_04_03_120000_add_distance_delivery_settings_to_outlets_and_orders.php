<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->boolean('pickup_enabled')->default(false)->after('phone');
            $table->boolean('delivery_enabled')->default(false)->after('pickup_enabled');
            $table->decimal('pickup_base_distance_km', 8, 2)->default(0)->after('delivery_enabled');
            $table->unsignedInteger('pickup_base_fee')->default(0)->after('pickup_base_distance_km');
            $table->unsignedInteger('pickup_extra_fee_per_km')->default(0)->after('pickup_base_fee');
            $table->decimal('delivery_base_distance_km', 8, 2)->default(0)->after('pickup_extra_fee_per_km');
            $table->unsignedInteger('delivery_base_fee')->default(0)->after('delivery_base_distance_km');
            $table->unsignedInteger('delivery_extra_fee_per_km')->default(0)->after('delivery_base_fee');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('pickup_distance_km', 8, 2)->nullable()->after('pickup_longitude');
            $table->decimal('delivery_distance_km', 8, 2)->nullable()->after('delivery_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_distance_km',
                'delivery_distance_km',
            ]);
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_enabled',
                'delivery_enabled',
                'pickup_base_distance_km',
                'pickup_base_fee',
                'pickup_extra_fee_per_km',
                'delivery_base_distance_km',
                'delivery_base_fee',
                'delivery_extra_fee_per_km',
            ]);
        });
    }
};
