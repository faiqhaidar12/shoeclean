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
        $outlets = \Illuminate\Support\Facades\DB::table('outlets')->where('slug', '')->orWhereNull('slug')->get();
        
        foreach ($outlets as $outlet) {
            $baseSlug = \Illuminate\Support\Str::slug($outlet->name);
            $slug = $baseSlug ?: 'outlet-' . $outlet->id;
            
            // Check for uniqueness
            $originalSlug = $slug;
            $counter = 2;
            while (\Illuminate\Support\Facades\DB::table('outlets')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            \Illuminate\Support\Facades\DB::table('outlets')->where('id', $outlet->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed for backfill
    }
};
