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
        // Create roles
        $ownerRole = \App\Models\Role::create(['name' => 'Owner', 'slug' => 'owner']);
        \App\Models\Role::create(['name' => 'Admin', 'slug' => 'admin']);
        \App\Models\Role::create(['name' => 'Staff', 'slug' => 'staff']);

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
