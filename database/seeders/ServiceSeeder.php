<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Outlet;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first outlet (or create one if none exists)
        $outlet = Outlet::first();
        
        if (!$outlet) {
            // Create a sample outlet if none exists
            $owner = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('slug', 'owner');
            })->first();
            
            if ($owner) {
                $outlet = Outlet::create([
                    'owner_id' => $owner->id,
                    'name' => 'Shoe Clean Central',
                    'address' => 'Jl. Sepatu Bersih No. 1, Jakarta',
                    'phone' => '081234567890',
                    'status' => 'active',
                ]);
            }
        }

        if (!$outlet) {
            return;
        }

        // Shoe Cleaning Services
        $services = [
            [
                'name' => 'Cuci Standar',
                'unit' => 'pasang',
                'price' => 35000,
                'status' => 'active',
            ],
            [
                'name' => 'Deep Clean',
                'unit' => 'pasang',
                'price' => 50000,
                'status' => 'active',
            ],
            [
                'name' => 'Deep Clean + Whitening',
                'unit' => 'pasang',
                'price' => 75000,
                'status' => 'active',
            ],
            [
                'name' => 'Treatment Kulit (Leather Care)',
                'unit' => 'pasang',
                'price' => 85000,
                'status' => 'active',
            ],
            [
                'name' => 'Repaint / Pewarnaan Ulang',
                'unit' => 'pasang',
                'price' => 150000,
                'status' => 'active',
            ],
            [
                'name' => 'Unyellowing Sole',
                'unit' => 'pasang',
                'price' => 60000,
                'status' => 'active',
            ],
            [
                'name' => 'Suede Cleaning',
                'unit' => 'pasang',
                'price' => 65000,
                'status' => 'active',
            ],
            [
                'name' => 'Fast Clean (Express 1 Hari)',
                'unit' => 'pasang',
                'price' => 55000,
                'status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, ['outlet_id' => $outlet->id]));
        }
    }
}
