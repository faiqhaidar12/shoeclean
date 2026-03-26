<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Subscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.superadmin')]
class SubscriptionInsights extends Component
{
    use WithPagination;

    public $planFilter = '';
    public $statusFilter = '';

    public function updated($property)
    {
        if (in_array($property, ['planFilter', 'statusFilter'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $owners = User::whereHas('roles', fn($q) => $q->where('slug', 'owner'))
            ->with(['subscriptions' => fn($q) => $q->latest('started_at')])
            ->get();

        $freeOwnersCount = $owners->filter(fn($owner) => $owner->currentPlan() === 'free')->count();
        $proOwnersCount = $owners->filter(fn($owner) => $owner->currentPlan() === 'pro')->count();
        $businessOwnersCount = $owners->filter(fn($owner) => $owner->currentPlan() === 'business')->count();

        $expiringSubscriptions = Subscription::with('user:id,name,email')
            ->active()
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->orderBy('expires_at')
            ->limit(6)
            ->get();

        $subscriptions = Subscription::with('user:id,name,email')
            ->when($this->planFilter, fn($query) => $query->where('plan', $this->planFilter))
            ->when($this->statusFilter === 'active', fn($query) => $query->active())
            ->when($this->statusFilter === 'expired', fn($query) => $query->expired())
            ->latest('started_at')
            ->paginate(12);

        return view('livewire.super-admin.subscription-insights', [
            'freeOwnersCount' => $freeOwnersCount,
            'proOwnersCount' => $proOwnersCount,
            'businessOwnersCount' => $businessOwnersCount,
            'expiringSubscriptions' => $expiringSubscriptions,
            'subscriptions' => $subscriptions,
        ]);
    }
}
