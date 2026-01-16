<?php

namespace App\Livewire\Promos;

use Livewire\Component;
use App\Models\Promo;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreatePromo extends Component
{
    public $outlet_id = null; // Null = all outlets
    public $code = '';
    public $name = '';
    public $type = 'percentage';
    public $value;
    public $min_order = 0;
    public $max_discount;
    public $max_uses;
    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->start_date = today()->format('Y-m-d');
        $this->end_date = today()->addMonth()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|string|max:20|unique:promos,code',
            'name' => 'required|string|max:100',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|integer|min:1',
            'min_order' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Promo::create([
            'outlet_id' => $this->outlet_id,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'min_order' => $this->min_order ?? 0,
            'max_discount' => $this->max_discount,
            'max_uses' => $this->max_uses,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        return redirect()->route('promos.index');
    }

    public function render()
    {
        $user = auth()->user();
        $outlets = $user->isOwner() ? $user->ownedOutlets : collect();

        return view('livewire.promos.create-promo', [
            'outlets' => $outlets,
        ]);
    }
}
