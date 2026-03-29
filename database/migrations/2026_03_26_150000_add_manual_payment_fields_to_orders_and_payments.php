<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function canAlterEnums(): bool
    {
        return DB::getDriverName() !== 'sqlite';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_proof_path')->nullable()->after('payment_method');
            $table->string('payment_proof_original_name')->nullable()->after('payment_proof_path');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof_original_name');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_proof_uploaded_at');
            $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')->constrained('users')->nullOnDelete();
            $table->text('payment_notes')->nullable()->after('payment_verified_by');
        });

        if ($this->canAlterEnums()) {
            DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid', 'waiting_confirmation', 'paid') NOT NULL DEFAULT 'unpaid'");
            DB::statement("ALTER TABLE payments MODIFY method ENUM('cash', 'qris', 'manual_transfer', 'midtrans') NOT NULL DEFAULT 'cash'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->canAlterEnums()) {
            DB::statement("ALTER TABLE payments MODIFY method ENUM('cash', 'midtrans') NOT NULL DEFAULT 'cash'");
            DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid'");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_verified_by');
            $table->dropColumn([
                'payment_method',
                'payment_proof_path',
                'payment_proof_original_name',
                'payment_proof_uploaded_at',
                'payment_verified_at',
                'payment_notes',
            ]);
        });
    }
};
