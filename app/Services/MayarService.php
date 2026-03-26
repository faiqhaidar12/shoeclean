<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MayarService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('mayar.api_url'), '/');
        $this->apiKey = config('mayar.api_key');
    }

    /**
     * Create an invoice for top-up quota purchase.
     */
    public function createInvoice(array $customerInfo, string $description, array $items, string $redirectUrl, ?string $expiredAt = null): array
    {
        $payload = [
            'name' => $customerInfo['name'],
            'email' => $customerInfo['email'],
            'mobile' => $customerInfo['mobile'],
            'redirectUrl' => $redirectUrl,
            'description' => $description,
            'expiredAt' => $expiredAt ?? now()->addDays(1)->toIso8601String(),
            'items' => $items,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post("{$this->apiUrl}/hl/v1/invoice/create", $payload);

        if ($response->failed()) {
            Log::error('Mayar invoice creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gagal membuat invoice: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Register a customer for membership (subscription).
     */
    public function registerMembership(string $productId, string $tierId, int $monthlyPeriod, array $customerInfo, ?int $trialCredit = null): array
    {
        $payload = [
            'productId' => $productId,
            'membershipTierId' => $tierId,
            'membershipMonthlyPeriod' => $monthlyPeriod,
            'customerInfo' => $customerInfo,
        ];

        if ($trialCredit !== null) {
            $payload['trialCredit'] = $trialCredit;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post("{$this->apiUrl}/credit/v1/credit/membership/customer/regist", $payload);

        if ($response->failed()) {
            Log::error('Mayar membership registration failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gagal mendaftarkan membership: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Generate an immutable checkout link.
     */
    public function generateCheckoutLink(string $productId, array $customerInfo, ?int $creditAmount = null): array
    {
        $payload = [
            'productId' => $productId,
            'customerInfo' => $customerInfo,
        ];

        if ($creditAmount !== null) {
            $payload['creditAmount'] = $creditAmount;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post("{$this->apiUrl}/credit/v1/credit/generate/immutable/checkout", $payload);

        if ($response->failed()) {
            Log::error('Mayar checkout link generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gagal generate checkout link: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Register webhook URL.
     */
    public function registerWebhook(string $url): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post("{$this->apiUrl}/hl/v1/webhook/register", [
            'urlHook' => $url,
        ]);

        if ($response->failed()) {
            Log::error('Mayar webhook registration failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gagal mendaftarkan webhook: ' . $response->body());
        }

        return $response->json();
    }
}
