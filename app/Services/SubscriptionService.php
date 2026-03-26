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
    public function activateSubscription(User $owner, string $plan, ?string $mayarTransactionId = null, ?string $mayarMemberId = null, ?int $monthlyPeriod = 1): Subscription
    {
        // Expire any existing active subscriptions
        $owner->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $expiresAt = now()->addMonths($monthlyPeriod);

        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'plan' => $plan,
            'status' => 'active',
            'mayar_transaction_id' => $mayarTransactionId,
            'mayar_member_id' => $mayarMemberId,
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
    public function addQuota(User $owner, int $amount, ?string $mayarTransactionId = null): OrderQuota
    {
        $quota = OrderQuota::create([
            'user_id' => $owner->id,
            'quota_total' => $amount,
            'quota_used' => 0,
            'mayar_transaction_id' => $mayarTransactionId,
            'purchased_at' => now(),
        ]);

        Log::info("Order quota added", [
            'user_id' => $owner->id,
            'quota' => $amount,
            'transaction_id' => $mayarTransactionId,
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
        return [
            'free' => [
                'name' => 'Free',
                'subtitle' => 'Untuk mulai operasional',
                'price' => 0,
                'price_label' => 'Gratis',
                'order_limit' => config('mayar.plans.free.order_limit', 100),
                'max_outlets' => 1,
                'description' => 'Cocok untuk toko baru yang ingin mulai mencatat order dan mencoba alur operasional digital.',
                'cta' => 'Mulai Gratis',
                'features' => [
                    '1 outlet',
                    '100 order total',
                    'Tracking customer',
                    'QRIS outlet & pembayaran manual',
                    'Laporan dasar',
                ],
            ],
            'pro' => [
                'name' => 'Pro',
                'subtitle' => 'Untuk 1 outlet yang sudah aktif',
                'price' => config('mayar.plans.pro.price', 75000),
                'price_label' => 'Rp75.000/bulan',
                'order_limit' => null,
                'max_outlets' => 1,
                'description' => 'Paket terbaik untuk 1 outlet yang ingin operasional tanpa batas dan fitur bisnis yang lebih lengkap.',
                'cta' => 'Upgrade ke Pro',
                'features' => [
                    '1 outlet',
                    'Unlimited order',
                    'Promo & voucher',
                    'Export laporan PDF/Excel',
                    'Kelola admin/staff outlet',
                ],
            ],
            'business' => [
                'name' => 'Business',
                'subtitle' => 'Untuk bisnis multi cabang',
                'price' => config('mayar.plans.business.price', 200000),
                'price_label' => 'Rp200.000/bulan',
                'order_limit' => null,
                'max_outlets' => null,
                'description' => 'Untuk owner yang mengelola banyak outlet dan butuh kontrol operasional serta laporan lintas cabang.',
                'cta' => 'Upgrade ke Business',
                'features' => [
                    'Semua fitur Pro',
                    'Unlimited outlet',
                    'Unlimited order',
                    'Laporan multi-cabang',
                    'Kontrol bisnis lintas cabang',
                ],
            ],
            'topup' => [
                'name' => 'Top-up Kuota',
                'subtitle' => 'Tambahan untuk paket Free',
                'price' => config('mayar.topup.price', 100000),
                'price_label' => 'Rp100.000',
                'quota' => config('mayar.topup.quota', 500),
                'description' => 'Saat kuota Free habis, Anda bisa tambah 500 order tanpa harus langsung upgrade paket.',
                'cta' => 'Beli 500 Order',
            ],
        ];
    }
}
