<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentTransaction;
use App\Services\PaymentTransactionService;
use App\Services\PricingCatalogService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class SubscriptionManagementController
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->canManageSubscription($user), 403);

        $owner = $this->resolveOwner($user);
        $this->reconcilePendingCheckout($request, $owner);
        $this->reconcileRecentPendingTransactions($owner);

        $service = app(SubscriptionService::class);
        $activeSubscription = $owner?->activeSubscription();
        $orderLimitInfo = $service->checkOrderLimit($user);
        $planDetails = $service->getPlanDetails();
        $ownedOutlets = $owner?->ownedOutlets()->orderBy('name')->get(['id', 'name', 'slug', 'status']) ?? collect();
        $subscriptionHistory = $owner?->subscriptions()->with('paymentTransaction')->latest('started_at')->limit(6)->get() ?? collect();
        $quotaHistory = $owner?->orderQuotas()->with('paymentTransaction')->latest('purchased_at')->limit(6)->get() ?? collect();
        $pendingTransactions = $owner
            ? PaymentTransaction::query()
                ->where('user_id', $owner->id)
                ->whereIn('kind', ['subscription', 'topup'])
                ->whereNull('billable_type')
                ->whereNotIn('status_code', ['00', '02', '03'])
                ->latest('id')
                ->limit(5)
                ->get()
            : collect();

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
                'payment_gateway' => $activeSubscription->payment_gateway,
                'transaction_id' => $activeSubscription->gateway_transaction_id ?? $activeSubscription->mayar_transaction_id,
                'reference_id' => $activeSubscription->gateway_reference ?? $activeSubscription->mayar_member_id,
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
                'pro' => '/api/subscription/checkout/pro',
                'business' => '/api/subscription/checkout/business',
                'topup' => '/api/subscription/checkout/topup',
            ],
            'pending_transactions' => $pendingTransactions
                ->map(fn ($transaction) => [
                    'id' => $transaction->id,
                    'kind' => $transaction->kind,
                    'plan_key' => $transaction->plan_key,
                    'plan_label' => $transaction->kind === 'topup'
                        ? 'Top Up Pesanan'
                        : strtoupper((string) $transaction->plan_key),
                    'merchant_order_id' => $transaction->merchant_order_id,
                    'reference' => $transaction->reference,
                    'payment_method' => $transaction->payment_method,
                    'status_code' => $transaction->status_code,
                    'status_message' => $transaction->status_message,
                    'amount' => $transaction->amount,
                    'amount_label' => 'Rp ' . number_format((int) $transaction->amount, 0, ',', '.'),
                    'payment_url' => data_get($transaction->checkout_payload, 'paymentUrl'),
                    'popup_script_url' => data_get($transaction->checkout_payload, 'popupScriptUrl'),
                    'expires_at' => optional($transaction->expires_at)->toIso8601String(),
                    'created_at' => optional($transaction->created_at)->toIso8601String(),
                    'last_synced_at' => optional($transaction->last_synced_at)->toIso8601String(),
                    'is_expired' => $transaction->expires_at?->isPast() ?? false,
                ])
                ->values(),
            'subscription_history' => $subscriptionHistory
                ->map(fn ($subscription) => [
                    'id' => $subscription->id,
                    'plan' => $subscription->plan,
                    'status' => $subscription->status,
                    'started_at' => optional($subscription->started_at)->toIso8601String(),
                    'expires_at' => optional($subscription->expires_at)->toIso8601String(),
                    'payment_gateway' => $subscription->payment_gateway,
                    'transaction_id' => $subscription->gateway_transaction_id ?? $subscription->mayar_transaction_id,
                    'reference_id' => $subscription->gateway_reference ?? $subscription->mayar_member_id,
                    'amount' => data_get($planDetails, "{$subscription->plan}.price"),
                    'amount_label' => data_get($planDetails, "{$subscription->plan}.price_label"),
                    'receipt_item' => 'Langganan ' . strtoupper((string) $subscription->plan),
                    'payment_transaction' => $subscription->paymentTransaction ? [
                        'gateway' => $subscription->paymentTransaction->gateway,
                        'merchant_order_id' => $subscription->paymentTransaction->merchant_order_id,
                        'reference' => $subscription->paymentTransaction->reference,
                        'payment_method' => $subscription->paymentTransaction->payment_method,
                        'status_code' => $subscription->paymentTransaction->status_code,
                        'status_message' => $subscription->paymentTransaction->status_message,
                        'paid_at' => optional($subscription->paymentTransaction->paid_at)->toIso8601String(),
                    ] : null,
                ])->values(),
            'quota_history' => $quotaHistory
                ->map(fn ($quota) => [
                    'id' => $quota->id,
                    'quota_total' => $quota->quota_total,
                    'quota_used' => $quota->quota_used,
                    'quota_remaining' => $quota->remainingQuota(),
                    'purchased_at' => optional($quota->purchased_at)->toIso8601String(),
                    'payment_gateway' => $quota->payment_gateway,
                    'transaction_id' => $quota->gateway_transaction_id ?? $quota->mayar_transaction_id,
                    'reference_id' => $quota->gateway_reference,
                    'amount' => data_get($planDetails, 'topup.price'),
                    'amount_label' => data_get($planDetails, 'topup.price_label'),
                    'receipt_item' => 'Top Up ' . number_format((int) $quota->quota_total) . ' Pesanan',
                    'payment_transaction' => $quota->paymentTransaction ? [
                        'gateway' => $quota->paymentTransaction->gateway,
                        'merchant_order_id' => $quota->paymentTransaction->merchant_order_id,
                        'reference' => $quota->paymentTransaction->reference,
                        'payment_method' => $quota->paymentTransaction->payment_method,
                        'status_code' => $quota->paymentTransaction->status_code,
                        'status_message' => $quota->paymentTransaction->status_message,
                        'paid_at' => optional($quota->paymentTransaction->paid_at)->toIso8601String(),
                    ] : null,
                ])->values(),
        ]);
    }

    public function checkout(Request $request, string $plan): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->canManageSubscription($user), 403);

        $owner = $this->resolveOwner($user);
        abort_unless($owner, 403);

        $planDetails = app(SubscriptionService::class)->getPlanDetails();
        $selectedPlan = $planDetails[$plan] ?? null;

        abort_unless($selectedPlan, 404, 'Paket tidak ditemukan.');
        abort_if($plan === 'free', 422, 'Paket gratis tidak memerlukan pembayaran.');
        abort_if(!($selectedPlan['is_published'] ?? false), 422, 'Paket ini belum dipublikasikan.');

        $returnUrl = $request->string('return_url')->toString();
        if ($returnUrl === '') {
            $returnUrl = route('subscription');
        }

        $payment = app(\App\Services\DuitkuService::class)->createCheckout($owner, $plan, $selectedPlan, $returnUrl);
        $paymentUrl = data_get($payment, 'paymentUrl');
        $merchantOrderId = data_get($payment, 'merchantOrderId');

        if (!$paymentUrl) {
            abort(500, 'Link pembayaran Duitku tidak ditemukan.');
        }

        if ($merchantOrderId) {
            app(PaymentTransactionService::class)->recordCheckout($owner, $plan, $selectedPlan, $payment);
            $request->session()->put('duitku_pending_checkout', [
                'user_id' => $owner->id,
                'plan' => $plan,
                'merchant_order_id' => $merchantOrderId,
                'created_at' => now()->toIso8601String(),
            ]);
        }

        return response()->json([
            'payment_url' => $paymentUrl,
            'reference' => data_get($payment, 'reference'),
            'merchant_order_id' => $merchantOrderId,
            'payment_method' => data_get($payment, 'paymentMethod'),
            'popup_script_url' => data_get($payment, 'popupScriptUrl'),
        ]);
    }

    protected function canManageSubscription(User $user): bool
    {
        return $user->isOwner() || $user->ownedOutlets()->exists();
    }

    protected function resolveOwner(User $user): ?User
    {
        if ($user->isOwner()) {
            return $user;
        }

        if ($user->ownedOutlets()->exists()) {
            return $user;
        }

        return $user->getOwner();
    }

    protected function reconcilePendingCheckout(Request $request, ?User $owner): void
    {
        if (!$owner) {
            return;
        }

        $pending = $request->session()->get('duitku_pending_checkout');
        if (!is_array($pending)) {
            return;
        }

        if (($pending['user_id'] ?? null) !== $owner->id) {
            return;
        }

        $merchantOrderId = (string) ($pending['merchant_order_id'] ?? '');
        $plan = (string) ($pending['plan'] ?? '');

        if ($merchantOrderId === '' || $plan === '') {
            $request->session()->forget('duitku_pending_checkout');
            return;
        }

        try {
            $status = app(\App\Services\DuitkuService::class)->getTransactionStatus($merchantOrderId);
        } catch (\Throwable $exception) {
            \Log::warning('Duitku pending checkout sync failed', [
                'merchant_order_id' => $merchantOrderId,
                'user_id' => $owner->id,
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        $statusCode = (string) data_get($status, 'statusCode');
        $reference = data_get($status, 'reference');
        $plans = app(PricingCatalogService::class)->getPlanDetails();
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $paymentTransactionService = app(PaymentTransactionService::class);
        $paymentTransactionService->syncFromStatus($owner, $plan, $merchantOrderId, $status);

        if ($statusCode === '00') {
            if ($plan === 'topup') {
                $quota = $subscriptionService->addQuota(
                    $owner,
                    (int) data_get($plans, 'topup.quota', 0),
                    $merchantOrderId,
                    'duitku',
                    $reference,
                );
                $paymentTransactionService->attachBillable($merchantOrderId, $quota);
            } elseif (in_array($plan, ['pro', 'business'], true)) {
                $subscription = $subscriptionService->activateSubscription(
                    $owner,
                    $plan,
                    $merchantOrderId,
                    null,
                    1,
                    'duitku',
                    $reference,
                );
                $paymentTransactionService->attachBillable($merchantOrderId, $subscription);
            }

            $request->session()->forget('duitku_pending_checkout');
            return;
        }

        if (in_array($statusCode, ['02', '03'], true)) {
            $request->session()->forget('duitku_pending_checkout');
        }
    }

    protected function reconcileRecentPendingTransactions(?User $owner): void
    {
        if (!$owner) {
            return;
        }

        $pendingTransactions = PaymentTransaction::query()
            ->where('user_id', $owner->id)
            ->whereIn('kind', ['subscription', 'topup'])
            ->whereNull('billable_type')
            ->whereNotIn('status_code', ['02', '03'])
            ->latest('id')
            ->limit(5)
            ->get();

        if ($pendingTransactions->isEmpty()) {
            return;
        }

        $plans = app(PricingCatalogService::class)->getPlanDetails();
        $subscriptionService = app(SubscriptionService::class);
        $paymentTransactionService = app(PaymentTransactionService::class);
        $duitkuService = app(\App\Services\DuitkuService::class);

        foreach ($pendingTransactions as $transaction) {
            try {
                $status = $duitkuService->getTransactionStatus($transaction->merchant_order_id);
            } catch (\Throwable $exception) {
                \Log::warning('Duitku recent pending sync failed', [
                    'merchant_order_id' => $transaction->merchant_order_id,
                    'user_id' => $owner->id,
                    'message' => $exception->getMessage(),
                ]);

                continue;
            }

            $statusCode = (string) data_get($status, 'statusCode');
            $reference = data_get($status, 'reference');
            $paymentTransactionService->syncFromStatus($owner, $transaction->plan_key ?? 'pro', $transaction->merchant_order_id, $status);

            if ($statusCode !== '00') {
                continue;
            }

            if ($transaction->kind === 'topup') {
                $quota = $subscriptionService->addQuota(
                    $owner,
                    (int) data_get($plans, 'topup.quota', 0),
                    $transaction->merchant_order_id,
                    'duitku',
                    $reference,
                );
                $paymentTransactionService->attachBillable($transaction->merchant_order_id, $quota);
                continue;
            }

            if (in_array($transaction->plan_key, ['pro', 'business'], true)) {
                $subscription = $subscriptionService->activateSubscription(
                    $owner,
                    $transaction->plan_key,
                    $transaction->merchant_order_id,
                    null,
                    1,
                    'duitku',
                    $reference,
                );
                $paymentTransactionService->attachBillable($transaction->merchant_order_id, $subscription);
            }
        }
    }
}
