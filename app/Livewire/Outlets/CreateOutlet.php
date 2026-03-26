<?php

namespace App\Livewire\Outlets;

use App\Models\Outlet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class CreateOutlet extends Component
{
    use WithFileUploads;

    public $name;
    public $slug;
    public $address;
    public $phone;
    public $pickup_fee = 10000;
    public $delivery_fee = 10000;
    public $qris_image;
    public $qris_notes = '';

    public function mount()
    {
        if (!auth()->user()->canCreateOutlet()) {
            session()->flash('error', 'Limit outlet Anda telah habis. Silakan langganan paket Business untuk menambah cabang.');
            return redirect()->route('subscription');
        }
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public $province_id = '';
    public $province_name = '';
    public $city_id = '';
    public $city_name = '';
    public $district_id = '';
    public $district_name = '';

    public $cities = [];
    public $districts = [];

    public function getProvincesProperty()
    {
        return Cache::remember('emsifa_provinces', 86400, function () {
            try {
                $response = Http::timeout(5)->get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                return $response->json() ?? [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public function updatedProvinceId($value)
    {
        $this->city_id = '';
        $this->city_name = '';
        $this->district_id = '';
        $this->district_name = '';
        $this->cities = [];
        $this->districts = [];

        if ($value) {
            $province = collect($this->provinces)->firstWhere('id', $value);
            if (is_array($province)) {
                $this->province_name = $province['name'];
            }

            try {
                $response = Http::timeout(5)->get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$value}.json");
                $this->cities = $response->json() ?? [];
            } catch (\Exception $e) {
                $this->cities = [];
            }
        }
    }

    public function updatedCityId($value)
    {
        $this->district_id = '';
        $this->district_name = '';
        $this->districts = [];

        if ($value) {
            $city = collect($this->cities)->firstWhere('id', $value);
            if (is_array($city)) {
                $this->city_name = $city['name'];
            }

            try {
                $response = Http::timeout(5)->get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$value}.json");
                $this->districts = $response->json() ?? [];
            } catch (\Exception $e) {
                $this->districts = [];
            }
        }
    }

    public function updatedDistrictId($value)
    {
        if ($value) {
            $district = collect($this->districts)->firstWhere('id', $value);
            if (is_array($district)) {
                $this->district_name = $district['name'];
            }
        }
    }

    public function save()
    {
        if (!auth()->user()->canCreateOutlet()) {
            session()->flash('error', 'Limit outlet Anda telah habis. Silakan langganan paket Business untuk menambah cabang.');
            return redirect()->route('subscription');
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'pickup_fee' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'qris_notes' => 'nullable|string|max:1000',
            'province_id' => 'required|string',
            'city_id' => 'required|string',
            'district_id' => 'nullable|string',
        ]);

        $qrisImagePath = $this->qris_image?->store('qris', 'public');

        // Auto-generate unique slug
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 2;
        while (Outlet::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        Outlet::create([
            'owner_id' => auth()->id(),
            'name' => $this->name,
            'slug' => $slug,
            'address' => $this->address,
            'phone' => $this->phone,
            'pickup_fee' => $this->pickup_fee,
            'delivery_fee' => $this->delivery_fee,
            'qris_image_path' => $qrisImagePath,
            'qris_image_original_name' => $this->qris_image?->getClientOriginalName(),
            'qris_notes' => $this->qris_notes ?: null,
            'status' => 'active',
            'province_id' => $this->province_id,
            'province_name' => $this->province_name,
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'district_id' => $this->district_id,
            'district_name' => $this->district_name,
        ]);

        return redirect()->route('outlets.index');
    }

    public function render()
    {
        return view('livewire.outlets.create-outlet');
    }
}
