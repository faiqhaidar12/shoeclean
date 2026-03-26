<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add slug column (without unique yet)
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('slug')->default('')->after('name');
        });

        // Backfill slugs using raw DB (not Eloquent, since model may not have slug in fillable yet)
        $outlets = DB::table('outlets')->get();
        foreach ($outlets as $outlet) {
            $baseSlug = Str::slug($outlet->name);
            $slug = $baseSlug ?: 'outlet-' . $outlet->id;
            $counter = 2;
            while (DB::table('outlets')->where('slug', $slug)->where('id', '!=', $outlet->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            DB::table('outlets')->where('id', $outlet->id)->update(['slug' => $slug]);
        }

        // Now add unique index
        Schema::table('outlets', function (Blueprint $table) {
            $table->unique('slug');
        });

        // 2. Add order_source to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_source', ['admin', 'customer'])->default('admin')->after('discount_amount');
        });

        // 3. Make user_id nullable on orders
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_source');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
