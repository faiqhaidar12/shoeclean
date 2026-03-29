<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $ownerRole = \App\Models\Role::where('slug', 'owner')->firstOrFail();

        // Create owner user
        $owner = User::factory()->create([
            'name' => 'Owner Shoe Clean',
            'email' => 'owner@shoeclean.com',
            'password' => 'password',
        ]);
        $owner->roles()->attach($ownerRole);

        // Seed services
        $this->call(ServiceSeeder::class);
    }
}
