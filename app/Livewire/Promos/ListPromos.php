<?php

namespace App\Livewire\Promos;

use Livewire\Component;
use App\Models\Promo;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListPromos extends Component
{
    use WithPagination;

    public $search = '';

    public function delete($id)
    {
        Promo::findOrFail($id)->delete();
    }

    public function toggle($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);
    }

    public function render()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $outletIds = $user->ownedOutlets->pluck('id')->toArray();
        } else {
            $outletIds = [$user->outlet_id];
        }

        $promos = Promo::where(function ($q) use ($outletIds) {
                $q->whereIn('outlet_id', $outletIds)->orWhereNull('outlet_id');
            })
            ->when($this->search, fn($q) => $q->where('code', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.promos.list-promos', [
            'promos' => $promos,
        ]);
    }
}
