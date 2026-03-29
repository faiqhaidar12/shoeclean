<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Owner', 'slug' => 'owner'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Staff', 'slug' => 'staff'],
            ['name' => 'Super Admin', 'slug' => 'superadmin'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );
        }
    }
}
