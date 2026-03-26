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
    public $is_active = true;

    public function mount()
    {
        if (!auth()->user()->hasFeature('promos')) {
            session()->flash('error', 'Fitur promo tersedia mulai paket Pro.');
            $this->redirectRoute(auth()->user()->isOwner() ? 'subscription' : 'dashboard', navigate: true);
            return;
        }

        $this->start_date = today()->format('Y-m-d');
        $this->end_date = today()->addMonth()->format('Y-m-d');
    }

    public function save()
    {
        if (!auth()->user()->hasFeature('promos')) {
            $route = auth()->user()->isOwner() ? 'subscription' : 'dashboard';
            return redirect()->route($route)->with('error', 'Fitur promo tersedia mulai paket Pro.');
        }

        $this->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'code' => 'required|string|max:20|unique:promos,code',
            'name' => 'required|string|max:100',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|integer|min:1',
            'min_order' => 'nullable|integer|min:0',
            'max_discount' => 'nullable|integer|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Promo::create([
            'outlet_id' => $this->outlet_id ?: null,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'min_order' => $this->min_order ?: 0,
            'max_discount' => $this->max_discount ?: null,
            'max_uses' => $this->max_uses ?: null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
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
