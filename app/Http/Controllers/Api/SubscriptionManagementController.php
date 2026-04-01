<?php

namespace App\Http\Controllers\Api;

use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionManagementController
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isOwner(), 403);

        $owner = $user->getOwner();
        $service = app(SubscriptionService::class);
        $activeSubscription = $owner?->activeSubscription();
        $orderLimitInfo = $service->checkOrderLimit($user);
        $planDetails = $service->getPlanDetails();
        $ownedOutlets = $owner?->ownedOutlets()->orderBy('name')->get(['id', 'name', 'slug', 'status']) ?? collect();

        return response()->json([
            'owner_name' => $owner?->name ?? $user->name,
            'current_plan' => $owner?->currentPlan() ?? 'free',
            'active_subscription' => $activeSubscription ? [
                'id' => $activeSubscription->id,
                'plan' => $activeSubscription->plan,
                'status' => $activeSubscription->status,
                'started_at' => optional($activeSubscription->started_at)->toIso8601String(),
                'expires_at' => optional($activeSubscription->expires_at)->toIso8601String(),
                'days_remaining' => $activeSubscription->daysRemaining(),
                'mayar_transaction_id' => $activeSubscription->mayar_transaction_id,
                'mayar_member_id' => $activeSubscription->mayar_member_id,
            ] : null,
            'order_limit' => $orderLimitInfo,
            'outlet_capacity' => [
                'current' => $ownedOutlets->count(),
                'max' => $owner?->maxOutlets(),
                'can_create' => $owner?->canCreateOutlet() ?? false,
            ],
            'outlets' => $ownedOutlets->map(fn ($outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
                'status' => $outlet->status,
            ])->values(),
            'plan_details' => $planDetails,
            'payment_links' => [
                'pro' => $this->paymentUrl('https://faiq-haidar.mayar.shop/pl/bulanan-pro', $owner?->name, $owner?->email),
                'business' => $this->paymentUrl('https://faiq-haidar.mayar.shop/pl/bulanan-business', $owner?->name, $owner?->email),
                'topup' => $this->paymentUrl('https://faiq-haidar.mayar.shop/pl/top-up-order-quota', $owner?->name, $owner?->email),
            ],
            'subscription_history' => $owner?->subscriptions()
                ->latest('started_at')
                ->limit(6)
                ->get()
                ->map(fn ($subscription) => [
                    'id' => $subscription->id,
                    'plan' => $subscription->plan,
                    'status' => $subscription->status,
                    'started_at' => optional($subscription->started_at)->toIso8601String(),
                    'expires_at' => optional($subscription->expires_at)->toIso8601String(),
                    'mayar_transaction_id' => $subscription->mayar_transaction_id,
                    'amount' => data_get($planDetails, "{$subscription->plan}.price"),
                    'amount_label' => data_get($planDetails, "{$subscription->plan}.price_label"),
                    'receipt_item' => 'Langganan ' . strtoupper((string) $subscription->plan),
                ])->values(),
            'quota_history' => $owner?->orderQuotas()
                ->latest('purchased_at')
                ->limit(6)
                ->get()
                ->map(fn ($quota) => [
                    'id' => $quota->id,
                    'quota_total' => $quota->quota_total,
                    'quota_used' => $quota->quota_used,
                    'quota_remaining' => $quota->remainingQuota(),
                    'purchased_at' => optional($quota->purchased_at)->toIso8601String(),
                    'mayar_transaction_id' => $quota->mayar_transaction_id,
                    'amount' => data_get($planDetails, 'topup.price'),
                    'amount_label' => data_get($planDetails, 'topup.price_label'),
                    'receipt_item' => 'Top Up ' . number_format((int) $quota->quota_total) . ' Order',
                ])->values(),
        ]);
    }

    protected function paymentUrl(string $baseUrl, ?string $name, ?string $email): string
    {
        return $baseUrl
            . '?email=' . urlencode((string) $email)
            . '&name=' . urlencode((string) $name);
    }
}
