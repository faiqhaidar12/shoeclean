<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected ?self $resolvedOwnerCache = null;
    protected bool $resolvedOwnerLoaded = false;
    protected ?\App\Models\Subscription $activeSubscriptionCache = null;
    protected bool $activeSubscriptionLoaded = false;
    protected ?string $currentPlanCache = null;
    protected bool $currentPlanLoaded = false;
    protected array $featureAccessCache = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'outlet_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function outlet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function ownedOutlets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Outlet::class, 'owner_id');
    }

    public function allOutletIds(): array
    {
        if ($this->isOwner()) {
            return $this->ownedOutlets->pluck('id')->toArray();
        }

        if ($this->outlet) {
            // For Staff/Admin, get all outlets of their owner
            return \App\Models\Outlet::where('owner_id', $this->outlet->owner_id)->pluck('id')->toArray();
        }

        return $this->outlet_id ? [$this->outlet_id] : [];
    }

    // ─── Subscription & Quota ──────────────────────────────────

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?\App\Models\Subscription
    {
        if ($this->activeSubscriptionLoaded) {
            return $this->activeSubscriptionCache;
        }

        if ($this->relationLoaded('subscriptions')) {
            $this->activeSubscriptionCache = $this->subscriptions
                ->filter(function ($subscription) {
                    return $subscription->status === 'active'
                        && (is_null($subscription->expires_at) || $subscription->expires_at->isFuture());
                })
                ->sortByDesc('started_at')
                ->first();
        } else {
            $this->activeSubscriptionCache = $this->subscriptions()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->latest('started_at')
                ->first();
        }

        $this->activeSubscriptionLoaded = true;

        return $this->activeSubscriptionCache;
    }

    public function orderQuotas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderQuota::class);
    }

    /**
     * Get the owner user. If this user is an owner, return self.
     * If staff/admin, return the outlet's owner.
     */
    public function getOwner(): ?User
    {
        if ($this->resolvedOwnerLoaded) {
            return $this->resolvedOwnerCache;
        }

        if ($this->isOwner()) {
            $this->resolvedOwnerCache = $this;
        } elseif ($this->relationLoaded('outlet') && $this->outlet) {
            if (!$this->outlet->relationLoaded('owner')) {
                $this->outlet->load('owner');
            }

            $this->resolvedOwnerCache = $this->outlet->owner;
        } elseif ($this->outlet) {
            $this->resolvedOwnerCache = $this->outlet->owner;
        } else {
            $this->resolvedOwnerCache = null;
        }

        $this->resolvedOwnerLoaded = true;

        return $this->resolvedOwnerCache;
    }

    /**
     * Get the current plan name (free/pro/business).
     */
    public function currentPlan(): string
    {
        if ($this->currentPlanLoaded) {
            return $this->currentPlanCache ?? 'free';
        }

        $owner = $this->getOwner();
        if (!$owner) {
            $this->currentPlanCache = 'free';
            $this->currentPlanLoaded = true;

            return 'free';
        }

        $sub = $owner->activeSubscription();
        $this->currentPlanCache = $sub ? $sub->plan : 'free';
        $this->currentPlanLoaded = true;

        return $this->currentPlanCache;
    }

    /**
     * Determine whether the current business plan grants access to a feature.
     */
    public function hasFeature(string $feature): bool
    {
        if (array_key_exists($feature, $this->featureAccessCache)) {
            return $this->featureAccessCache[$feature];
        }

        $plan = $this->currentPlan();

        $featureMatrix = [
            'free' => [],
            'pro' => ['promos', 'exports', 'team_management'],
            'business' => ['promos', 'exports', 'team_management', 'multi_outlet_reports'],
        ];

        return $this->featureAccessCache[$feature] = in_array($feature, $featureMatrix[$plan] ?? [], true);
    }

    /**
     * Determine whether the current plan can access combined multi-outlet reports.
     */
    public function canAccessMultiOutletReports(): bool
    {
        return $this->hasFeature('multi_outlet_reports');
    }

    /**
     * Resolve the active outlet ids for dashboard and reporting scope.
     */
    public function reportOutletIds(): array
    {
        if (!$this->isOwner()) {
            return $this->outlet_id ? [$this->outlet_id] : [];
        }

        $ownedOutletIds = $this->ownedOutlets()->orderBy('id')->pluck('id')->toArray();

        if (empty($ownedOutletIds)) {
            return [];
        }

        $selectedOutletId = session('current_outlet_id');

        if ($selectedOutletId && in_array((int) $selectedOutletId, $ownedOutletIds, true)) {
            return [(int) $selectedOutletId];
        }

        if ($this->canAccessMultiOutletReports()) {
            return $ownedOutletIds;
        }

        return [(int) $ownedOutletIds[0]];
    }

    /**
     * Count total orders across all owned outlets.
     */
    public function totalOrderCount(): int
    {
        $owner = $this->getOwner();
        if (!$owner) return 0;

        $outletIds = $owner->ownedOutlets->pluck('id')->toArray();
        return \App\Models\Order::whereIn('outlet_id', $outletIds)->count();
    }

    /**
     * Get remaining available order quota from top-ups.
     */
    public function availableQuota(): int
    {
        $owner = $this->getOwner();
        if (!$owner) return 0;

        return $owner->orderQuotas->sum(function ($q) {
            return $q->remainingQuota();
        });
    }

    /**
     * Get remaining orders before limit is hit. Returns null if unlimited.
     */
    public function remainingOrders(): ?int
    {
        $plan = $this->currentPlan();

        // Pro and Business = unlimited
        if (in_array($plan, ['pro', 'business'])) return null;

        $limit = config('mayar.plans.free.order_limit', 100);
        $totalOrders = $this->totalOrderCount();
        $extraQuota = $this->availableQuota();

        return max(0, ($limit + $extraQuota) - $totalOrders);
    }

    /**
     * Check if the owner can create a new order.
     */
    public function canCreateOrder(): bool
    {
        $remaining = $this->remainingOrders();

        // null = unlimited (Pro/Business)
        if ($remaining === null) return true;

        return $remaining > 0;
    }

    /**
     * Get maximum allowed outlets based on the current plan. Returns null if unlimited.
     */
    public function maxOutlets(): ?int
    {
        $plan = $this->currentPlan();
        return config('mayar.plans.' . $plan . '.max_outlets');
    }

    /**
     * Check if the owner can create a new outlet.
     */
    public function canCreateOutlet(): bool
    {
        $owner = $this->getOwner();
        if (!$owner) return false;

        $max = $this->maxOutlets();
        
        // null = unlimited (Business)
        if ($max === null) return true;

        return $owner->ownedOutlets()->count() < $max;
    }

    /**
     * Use an order slot: deduct from quota if over free limit.
     */
    public function useOrderSlot(): void
    {
        $owner = $this->getOwner();
        if (!$owner) return;

        $plan = $owner->currentPlan();
        if (in_array($plan, ['pro', 'business'])) return;

        $limit = config('mayar.plans.free.order_limit', 100);
        $totalOrders = $owner->totalOrderCount();

        // If we're over the free limit, deduct from quota
        if ($totalOrders >= $limit) {
            $quota = $owner->orderQuotas()
                ->whereRaw('quota_used < quota_total')
                ->oldest('purchased_at')
                ->first();

            if ($quota) {
                $quota->useQuota();
            }
        }
    }
}
