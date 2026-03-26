<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MayarWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        Log::info('Mayar webhook received', ['payload' => $payload]);

        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];

        if (!$event) {
            // Try parsing from the payload string format
            if (isset($payload['payload'])) {
                $parsed = json_decode($payload['payload'], true);
                $event = $parsed['event'] ?? null;
                $data = $parsed['data'] ?? [];
            }
        }

        if (!$event) {
            return response()->json(['message' => 'No event type found'], 400);
        }

        try {
            match ($event) {
                'payment.received' => $this->handlePaymentReceived($data),
                'membership.newMemberRegistered' => $this->handleNewMember($data),
                'membership.memberExpired' => $this->handleMemberExpired($data),
                'membership.memberUnsubscribed' => $this->handleMemberUnsubscribed($data),
                default => Log::info("Unhandled Mayar event: {$event}", $data),
            };
        } catch (\Throwable $e) {
            Log::error('Mayar webhook processing error', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Error processing webhook'], 500);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    protected function handlePaymentReceived(array $data): void
    {
        $transactionId = $data['transactionId'] ?? $data['id'] ?? null;
        $status = $data['status'] ?? $data['transactionStatus'] ?? null;
        $customerEmail = $data['customerEmail'] ?? null;
        $productType = $data['productType'] ?? null;
        $amount = $data['amount'] ?? 0;

        if (strtoupper($status) !== 'SUCCESS') {
            Log::info('Payment not successful, skipping', ['status' => $status]);
            return;
        }

        // Find the owner user by email
        $owner = User::where('email', $customerEmail)->first();
        if (!$owner) {
            Log::warning('Mayar payment: owner not found', ['email' => $customerEmail]);
            return;
        }

        $subscriptionService = new SubscriptionService();

        // Check if this payment is for a subscription plan based on amount or product ID
        $proPrice = config('mayar.plans.pro.price', 75000);
        $businessPrice = config('mayar.plans.business.price', 200000);
        
        $isSubscription = $productType === 'membership' 
            || $this->isMembershipPayment($data)
            || $amount == $proPrice 
            || $amount == $businessPrice;

        if ($isSubscription) {
            // Determine plan based on amount or tier
            $plan = $this->determinePlan($data, $amount);
            $memberId = $data['memberId'] ?? null;
            
            $subscriptionService->activateSubscription(
                $owner,
                $plan,
                $transactionId,
                $memberId
            );

            Log::info('Subscription activated via webhook', [
                'user_id' => $owner->id,
                'plan' => $plan,
            ]);
        } else {
            // Top-up quota payment
            $quota = config('mayar.topup.quota', 500);
            $subscriptionService->addQuota($owner, $quota, $transactionId);

            Log::info('Quota added via webhook', [
                'user_id' => $owner->id,
                'quota' => $quota,
            ]);
        }
    }

    protected function handleNewMember(array $data): void
    {
        $customerEmail = $data['customerEmail'] ?? null;
        $memberId = $data['memberId'] ?? null;
        
        if (!$customerEmail) return;

        $owner = User::where('email', $customerEmail)->first();
        if (!$owner) {
            Log::warning('Mayar new member: owner not found', ['email' => $customerEmail]);
            return;
        }

        $plan = $this->determinePlan($data, $data['amount'] ?? 0);
        $subscriptionService = new SubscriptionService();
        $subscriptionService->activateSubscription($owner, $plan, null, $memberId);
    }

    protected function handleMemberExpired(array $data): void
    {
        $memberId = $data['memberId'] ?? null;
        if (!$memberId) return;

        $subscription = Subscription::where('mayar_member_id', $memberId)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'expired']);
            Log::info('Membership expired', ['member_id' => $memberId]);
        }
    }

    protected function handleMemberUnsubscribed(array $data): void
    {
        $memberId = $data['memberId'] ?? null;
        if (!$memberId) return;

        $subscriptionService = new SubscriptionService();
        $subscriptionService->handleCancellation($memberId);
    }

    /**
     * Check if this is a membership product payment.
     */
    protected function isMembershipPayment(array $data): bool
    {
        $productId = $data['productId'] ?? null;
        $membershipProductId = config('mayar.product_membership_id');

        return $productId && $membershipProductId && $productId === $membershipProductId;
    }

    /**
     * Determine the plan based on payment data.
     */
    protected function determinePlan(array $data, int $amount): string
    {
        // Check by tier ID first
        $tierId = $data['membershipTierId'] ?? null;
        
        if ($tierId === config('mayar.tier_business_id')) {
            return 'business';
        }
        if ($tierId === config('mayar.tier_pro_id')) {
            return 'pro';
        }

        // Fallback: determine by amount
        $businessPrice = config('mayar.plans.business.price', 200000);
        $proPrice = config('mayar.plans.pro.price', 75000);

        if ($amount >= $businessPrice) return 'business';
        if ($amount >= $proPrice) return 'pro';

        return 'pro'; // Default to pro for any membership payment
    }
}
