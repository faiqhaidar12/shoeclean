<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;
use Livewire\Attributes\Layout;

#[Layout('layouts.storefront')]
class SelectOutlet extends Component
{
    public $search = '';
    public $selected_province = '';
    public $selected_city = '';

    public function updatedSelectedProvince()
    {
        $this->selected_city = '';
    }

    public function render()
    {
        $provinces = Outlet::where('status', 'active')
            ->whereNotNull('province_id')
            ->select('province_id', 'province_name')
            ->distinct()
            ->orderBy('province_name')
            ->get();

        $citiesQuery = Outlet::where('status', 'active')
            ->whereNotNull('city_id');
        
        if ($this->selected_province) {
            $citiesQuery->where('province_id', $this->selected_province);
        }

        $cities = $citiesQuery->select('city_id', 'city_name')
            ->distinct()
            ->orderBy('city_name')
            ->get();

        $outlets = Outlet::where('status', 'active')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selected_province, function ($query) {
                $query->where('province_id', $this->selected_province);
            })
            ->when($this->selected_city, function ($query) {
                $query->where('city_id', $this->selected_city);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.select-outlet', [
            'outlets' => $outlets,
            'provinces' => $provinces,
            'cities' => $cities,
        ]);
    }
}
