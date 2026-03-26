<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use App\Services\MayarService;
use App\Services\SubscriptionService;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SubscriptionPage extends Component
{
    public $currentPlan;
    public $activeSubscription;
    public $orderLimitInfo;
    public $planDetails;
    public $loading = false;
    public $errorMessage = '';

    public $showReceiptModal = false;
    public $receiptData = null;

    public function mount()
    {
        $user = auth()->user();
        $owner = $user->getOwner();

        $subscriptionService = new SubscriptionService();
        $this->planDetails = $subscriptionService->getPlanDetails();
        $this->orderLimitInfo = $subscriptionService->checkOrderLimit($user);
        $this->currentPlan = $owner?->currentPlan() ?? 'free';
        $this->activeSubscription = $owner?->activeSubscription();
    }

    /**
     * Subscribe to a plan (Pro or Business).
     */
    public function subscribePlan(string $plan)
    {
        $this->loading = true;
        $this->errorMessage = '';

        try {
            $user = auth()->user();
            $owner = $user->getOwner();

            // Direct payment link for Pro and Business plans as requested
            if (in_array($plan, ['pro', 'business'])) {
                $baseUrl = $plan === 'pro' 
                    ? 'https://faiq-haidar.mayar.shop/pl/bulanan-pro'
                    : 'https://faiq-haidar.mayar.shop/pl/bulanan-business';
                
                $paymentUrl = $baseUrl
                    . '?email=' . urlencode($owner->email) 
                    . '&name=' . urlencode($owner->name);
                
                $this->loading = false;
                return redirect()->away($paymentUrl);
            }

            $productId = config('mayar.product_membership_id');
            $tierId = $plan === 'pro'
                ? config('mayar.tier_pro_id')
                : config('mayar.tier_business_id');

            if (!$productId || !$tierId) {
                // If Mayar product IDs are not configured, activate directly (for testing)
                $subscriptionService = new SubscriptionService();
                $subscriptionService->activateSubscription($owner, $plan);
                
                $this->currentPlan = $plan;
                $this->activeSubscription = $owner->activeSubscription();
                $this->orderLimitInfo = $subscriptionService->checkOrderLimit($user);
                
                session()->flash('success', 'Paket ' . ucfirst($plan) . ' berhasil diaktifkan!');
                $this->loading = false;
                return;
            }

            $mayarService = new MayarService();

            $response = $mayarService->registerMembership(
                $productId,
                $tierId,
                1, // monthly
                [
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'mobile' => '08000000000', // placeholder
                ]
            );

            // Get the payment link URL from the response
            $paymentUrl = $response['data']['paymentLinkUrl']
                ?? $response['paymentLinkUrl']
                ?? null;

            // Try to get specific tier payment URL
            $tiers = $response['data']['membershipTiers'] ?? $response['membershipTiers'] ?? [];
            foreach ($tiers as $tier) {
                if ($tier['id'] === $tierId && isset($tier['specificPaymentLinkUrl'])) {
                    $paymentUrl = $tier['specificPaymentLinkUrl'];
                    break;
                }
            }

            if ($paymentUrl) {
                $this->loading = false;
                return redirect()->away($paymentUrl);
            }

            $this->errorMessage = 'Gagal mendapatkan link pembayaran. Silakan coba lagi.';
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    public function buyQuota()
    {
        $this->loading = true;
        $this->errorMessage = '';

        try {
            $user = auth()->user();
            $owner = $user->getOwner();

            // Direct payment link for Top-up Quota
            $paymentUrl = 'https://faiq-haidar.mayar.shop/pl/top-up-order-quota'
                . '?email=' . urlencode($owner->email)
                . '&name=' . urlencode($owner->name);

            $this->loading = false;
            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->loading = false;
        }
    }

    public function showReceipt($id)
    {
        $sub = auth()->user()->subscriptions()->find($id);
        if ($sub && $sub->mayar_transaction_id) {
            $this->receiptData = [
                'transaction_id' => $sub->mayar_transaction_id,
                'date' => $sub->started_at->format('d M Y, H:i'),
                'item' => 'Paket Langganan ' . ucfirst($sub->plan),
                'amount' => $sub->plan === 'business' ? 200000 : ($sub->plan === 'pro' ? 75000 : 0),
                'status' => 'LUNAS',
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
            ];
            $this->showReceiptModal = true;
        }
    }

    public function showTopupReceipt($id)
    {
        $quota = auth()->user()->orderQuotas()->find($id);
        if ($quota && $quota->mayar_transaction_id) {
            $this->receiptData = [
                'transaction_id' => $quota->mayar_transaction_id,
                'date' => $quota->purchased_at->format('d M Y, H:i'),
                'item' => 'Top-up Kuota ' . number_format($quota->quota_total) . ' Order',
                'amount' => config('mayar.topup.price', 100000),
                'status' => 'LUNAS',
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
            ];
            $this->showReceiptModal = true;
        }
    }

    public function closeReceipt()
    {
        $this->showReceiptModal = false;
        $this->receiptData = null;
    }

    public function render()
    {
        return view('livewire.subscription.subscription-page');
    }
}
