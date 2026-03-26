<?php

namespace App\Livewire\Outlets;

use App\Models\Outlet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EditOutlet extends Component
{
    use WithFileUploads;

    public Outlet $outlet;
    public $name;
    public $slug;
    public $address;
    public $phone;
    public $status;
    public $pickup_fee;
    public $delivery_fee;
    public $qris_image;
    public $qris_notes = '';
    public $remove_qris = false;
    public $search = '';
    public $searchResults = [];

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

    public function mount(Outlet $outlet)
    {
        $user = auth()->user();

        $canEditAsOwner = $user->isOwner() && $outlet->owner_id === $user->id;
        $canEditAsAdmin = $user->isAdmin() && (int) $user->outlet_id === (int) $outlet->id;

        if (!$canEditAsOwner && !$canEditAsAdmin) {
            abort(403);
        }

        $this->outlet = $outlet;
        $this->name = $outlet->name;
        $this->slug = $outlet->slug;
        $this->address = $outlet->address;
        $this->phone = $outlet->phone;
        $this->status = $outlet->status;
        $this->pickup_fee = $outlet->pickup_fee;
        $this->delivery_fee = $outlet->delivery_fee;
        $this->qris_notes = $outlet->qris_notes ?? '';

        $this->province_id = $outlet->province_id ?? '';
        $this->province_name = $outlet->province_name ?? '';
        $this->city_id = $outlet->city_id ?? '';
        $this->city_name = $outlet->city_name ?? '';
        $this->district_id = $outlet->district_id ?? '';
        $this->district_name = $outlet->district_name ?? '';

        if ($this->province_id) {
            try {
                $response = Http::timeout(5)->get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$this->province_id}.json");
                $this->cities = $response->json() ?? [];
            } catch (\Exception $e) {
                $this->cities = [];
            }
        }

        if ($this->city_id) {
            try {
                $response = Http::timeout(5)->get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$this->city_id}.json");
                $this->districts = $response->json() ?? [];
            } catch (\Exception $e) {
                $this->districts = [];
            }
        }
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

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = \App\Models\User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->take(5)
            ->get();
    }

    public function assignAdmin($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $adminRole = \App\Models\Role::where('slug', 'admin')->first();
        if (!$user->roles->contains($adminRole->id)) {
            $user->roles()->attach($adminRole);
        }

        $user->update(['outlet_id' => $this->outlet->id]);

        $this->mount($this->outlet); 
        $this->search = '';
        $this->searchResults = [];
    }

    public function removeAdmin($userId)
    {
        $user = \App\Models\User::where('outlet_id', $this->outlet->id)->findOrFail($userId);
        $user->update(['outlet_id' => null]);
        
        $this->mount($this->outlet); 
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:outlets,slug,' . $this->outlet->id,
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'pickup_fee' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'qris_notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'province_id' => 'required|string',
            'city_id' => 'required|string',
            'district_id' => 'nullable|string',
        ]);

        $qrisImagePath = $this->outlet->qris_image_path;
        $qrisImageOriginalName = $this->outlet->qris_image_original_name;

        if ($this->remove_qris) {
            if ($this->outlet->qris_image_path) {
                Storage::disk('public')->delete($this->outlet->qris_image_path);
            }

            $qrisImagePath = null;
            $qrisImageOriginalName = null;
        }

        if ($this->qris_image) {
            if ($this->outlet->qris_image_path && !$this->remove_qris) {
                Storage::disk('public')->delete($this->outlet->qris_image_path);
            }

            $qrisImagePath = $this->qris_image->store('qris', 'public');
            $qrisImageOriginalName = $this->qris_image->getClientOriginalName();
        }

        $this->outlet->update([
            'name' => $this->name,
            'slug' => Str::slug($this->slug ?: $this->name),
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status,
            'pickup_fee' => $this->pickup_fee,
            'delivery_fee' => $this->delivery_fee,
            'qris_image_path' => $qrisImagePath,
            'qris_image_original_name' => $qrisImageOriginalName,
            'qris_notes' => $this->qris_notes ?: null,
            'province_id' => $this->province_id,
            'province_name' => $this->province_name,
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'district_id' => $this->district_id,
            'district_name' => $this->district_name,
        ]);

        return redirect()->route('outlets.index');
    }

    public function removeQris()
    {
        $this->remove_qris = true;
        $this->qris_image = null;
    }

    public function keepQris()
    {
        $this->remove_qris = false;
    }

    public function render()
    {
        return view('livewire.outlets.edit-outlet');
    }
}
