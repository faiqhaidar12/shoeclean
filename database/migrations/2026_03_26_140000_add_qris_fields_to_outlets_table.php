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
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('qris_image_path')->nullable()->after('delivery_fee');
            $table->string('qris_image_original_name')->nullable()->after('qris_image_path');
            $table->text('qris_notes')->nullable()->after('qris_image_original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn([
                'qris_image_path',
                'qris_image_original_name',
                'qris_notes',
            ]);
        });
    }
};
