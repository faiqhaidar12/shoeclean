<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use App\Services\DuitkuService;
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
            $selectedPlan = $this->planDetails[$plan] ?? null;

            if (!$selectedPlan) {
                throw new \RuntimeException('Paket tidak ditemukan.');
            }

            if (!($selectedPlan['is_published'] ?? false)) {
                throw new \RuntimeException('Paket ini belum dipublikasikan.');
            }

            $response = app(DuitkuService::class)->createCheckout(
                $owner,
                $plan,
                $selectedPlan,
                route('subscription'),
            );

            $paymentUrl = data_get($response, 'paymentUrl');

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
            $selectedPlan = $this->planDetails['topup'] ?? null;

            if (!$selectedPlan || !($selectedPlan['is_published'] ?? false)) {
                throw new \RuntimeException('Top-up belum dipublikasikan.');
            }

            $response = app(DuitkuService::class)->createCheckout(
                $owner,
                'topup',
                $selectedPlan,
                route('subscription'),
            );
            $paymentUrl = data_get($response, 'paymentUrl');

            if (!$paymentUrl) {
                throw new \RuntimeException('Gagal mendapatkan link pembayaran Duitku.');
            }

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
        if ($sub && ($sub->gateway_transaction_id || $sub->mayar_transaction_id)) {
            $amount = data_get($this->planDetails, "{$sub->plan}.price", 0);
            $this->receiptData = [
                'transaction_id' => $sub->gateway_transaction_id ?? $sub->mayar_transaction_id,
                'date' => $sub->started_at->format('d M Y, H:i'),
                'item' => 'Paket Langganan ' . ucfirst($sub->plan),
                'amount' => $amount,
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
        if ($quota && ($quota->gateway_transaction_id || $quota->mayar_transaction_id)) {
            $this->receiptData = [
                'transaction_id' => $quota->gateway_transaction_id ?? $quota->mayar_transaction_id,
                'date' => $quota->purchased_at->format('d M Y, H:i'),
                'item' => 'Top-up Kuota ' . number_format($quota->quota_total) . ' Pesanan',
                'amount' => data_get($this->planDetails, 'topup.price', 0),
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
