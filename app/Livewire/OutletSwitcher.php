<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;

class OutletSwitcher extends Component
{
    public $currentOutletId;

    public function mount()
    {
        $this->currentOutletId = session('current_outlet_id');
    }

    public function switchOutlet($outletId)
    {
        $user = auth()->user();
        
        if ($outletId === 'all' || $outletId === null || $outletId === '') {
            session()->forget('current_outlet_id');
            $this->currentOutletId = null;
        } else {
            // Convert to int for consistent comparison
            $outletId = (int) $outletId;
            
            // Verify ownership/access
            $outlet = Outlet::find($outletId);
            
            if (!$outlet) {
                session()->flash('error', 'Outlet tidak ditemukan');
                return $this->redirect(route('dashboard'), navigate: true);
            }
            
            // Check if user can access this outlet
            $canAccess = false;
            
            if ($user->isOwner()) {
                // Owner can access their owned outlets
                $canAccess = $user->ownedOutlets->contains('id', $outletId);
            } elseif ($user->isAdmin() && $user->outlet) {
                // Admins can access all outlets of their owner
                $canAccess = Outlet::where('owner_id', $user->outlet->owner_id)->where('id', $outletId)->exists();
            } else {
                // Staff can only access their assigned outlet
                $canAccess = (int) $user->outlet_id === $outletId;
            }
            
            if ($canAccess) {
                session(['current_outlet_id' => $outletId]);
                $this->currentOutletId = $outletId;
            } else {
                session()->flash('error', 'Anda tidak memiliki akses ke outlet ini');
            }
        }

        // Redirect back to the same page - use Livewire's native redirect
        $referer = request()->header('Referer');
        if ($referer) {
            return $this->redirect($referer, navigate: true);
        }
        
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            $outlets = $user->ownedOutlets;
        } elseif ($user->isAdmin() && $user->outlet) {
            // Admins can switch between all outlets belonging to the business
            $outlets = Outlet::where('owner_id', $user->outlet->owner_id)->get();
        } else {
            // Staff only sees their assigned outlet
            $outlets = $user->outlet ? collect([$user->outlet]) : collect();
        }
        
        return view('livewire.outlet-switcher', [
            'outlets' => $outlets
        ]);
    }
}
