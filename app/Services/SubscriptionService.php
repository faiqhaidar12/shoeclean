<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\OrderQuota;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Check if an owner can create a new order.
     * Returns info about the current plan and remaining orders.
     */
    public function checkOrderLimit(User $user): array
    {
        $owner = $user->getOwner();
        if (!$owner) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'plan' => 'free',
                'total_orders' => 0,
                'limit' => 100,
            ];
        }

        $plan = $owner->currentPlan();
        $remaining = $owner->remainingOrders();
        $totalOrders = $owner->totalOrderCount();
        $limit = config('mayar.plans.free.order_limit', 100);

        return [
            'allowed' => $owner->canCreateOrder(),
            'remaining' => $remaining,
            'plan' => $plan,
            'total_orders' => $totalOrders,
            'limit' => in_array($plan, ['pro', 'business']) ? null : $limit + $owner->availableQuota(),
        ];
    }

    /**
     * Activate a subscription for an owner.
     */
    public function activateSubscription(
        User $owner,
        string $plan,
        ?string $transactionId = null,
        ?string $memberId = null,
        ?int $monthlyPeriod = 1,
        ?string $paymentGateway = null,
        ?string $gatewayReference = null
    ): Subscription
    {
        if ($paymentGateway && ($transactionId || $gatewayReference)) {
            $existing = Subscription::query()
                ->when($paymentGateway, fn ($query) => $query->where('payment_gateway', $paymentGateway))
                ->where(function ($query) use ($transactionId, $gatewayReference) {
                    if ($transactionId) {
                        $query->orWhere('gateway_transaction_id', $transactionId)
                            ->orWhere('mayar_transaction_id', $transactionId);
                    }

                    if ($gatewayReference) {
                        $query->orWhere('gateway_reference', $gatewayReference)
                            ->orWhere('mayar_member_id', $gatewayReference);
                    }
                })
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $currentActive = $owner->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('started_at')
            ->first();

        if ($currentActive && $currentActive->plan === $plan) {
            $baseExpiry = $currentActive->expires_at && $currentActive->expires_at->isFuture()
                ? $currentActive->expires_at->copy()
                : now();

            $newExpiry = $baseExpiry->addMonths($monthlyPeriod);

            $currentActive->update([
                'payment_gateway' => $paymentGateway ?? $currentActive->payment_gateway,
                'mayar_transaction_id' => $paymentGateway === 'mayar' ? $transactionId : $currentActive->mayar_transaction_id,
                'mayar_member_id' => $paymentGateway === 'mayar' ? $memberId : $currentActive->mayar_member_id,
                'gateway_transaction_id' => $transactionId ?? $currentActive->gateway_transaction_id,
                'gateway_reference' => $gatewayReference ?? $currentActive->gateway_reference,
                'expires_at' => $newExpiry,
            ]);

            Log::info("Subscription renewed", [
                'user_id' => $owner->id,
                'plan' => $plan,
                'expires_at' => $newExpiry,
            ]);

            return $currentActive->fresh();
        }

        // Expire any existing active subscriptions
        $owner->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $expiresAt = now()->addMonths($monthlyPeriod);

        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'plan' => $plan,
            'status' => 'active',
            'payment_gateway' => $paymentGateway,
            'mayar_transaction_id' => $paymentGateway === 'mayar' ? $transactionId : null,
            'mayar_member_id' => $paymentGateway === 'mayar' ? $memberId : null,
            'gateway_transaction_id' => $transactionId,
            'gateway_reference' => $gatewayReference,
            'started_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        Log::info("Subscription activated", [
            'user_id' => $owner->id,
            'plan' => $plan,
            'expires_at' => $expiresAt,
        ]);

        return $subscription;
    }

    /**
     * Add top-up quota for an owner.
     */
    public function addQuota(
        User $owner,
        int $amount,
        ?string $transactionId = null,
        ?string $paymentGateway = null,
        ?string $gatewayReference = null
    ): OrderQuota
    {
        if ($paymentGateway && ($transactionId || $gatewayReference)) {
            $existing = OrderQuota::query()
                ->when($paymentGateway, fn ($query) => $query->where('payment_gateway', $paymentGateway))
                ->where(function ($query) use ($transactionId, $gatewayReference) {
                    if ($transactionId) {
                        $query->orWhere('gateway_transaction_id', $transactionId)
                            ->orWhere('mayar_transaction_id', $transactionId);
                    }

                    if ($gatewayReference) {
                        $query->orWhere('gateway_reference', $gatewayReference);
                    }
                })
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $quota = OrderQuota::create([
            'user_id' => $owner->id,
            'quota_total' => $amount,
            'quota_used' => 0,
            'payment_gateway' => $paymentGateway,
            'mayar_transaction_id' => $paymentGateway === 'mayar' ? $transactionId : null,
            'gateway_transaction_id' => $transactionId,
            'gateway_reference' => $gatewayReference,
            'purchased_at' => now(),
        ]);

        Log::info("Order quota added", [
            'user_id' => $owner->id,
            'quota' => $amount,
            'transaction_id' => $transactionId,
        ]);

        return $quota;
    }

    /**
     * Handle subscription expiry.
     */
    public function handleExpiry(User $owner): void
    {
        $owner->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        Log::info("Subscription expired", ['user_id' => $owner->id]);
    }

    /**
     * Handle subscription cancellation.
     */
    public function handleCancellation(string $mayarMemberId): void
    {
        $subscription = Subscription::where('mayar_member_id', $mayarMemberId)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
            Log::info("Subscription cancelled", [
                'user_id' => $subscription->user_id,
                'member_id' => $mayarMemberId,
            ]);
        }
    }

    /**
     * Get plan details for display.
     */
    public function getPlanDetails(): array
    {
        return app(PricingCatalogService::class)->getPlanDetails();
    }
}
