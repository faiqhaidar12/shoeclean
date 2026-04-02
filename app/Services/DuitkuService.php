<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DuitkuService
{
    protected string $merchantCode;
    protected string $apiKey;
    protected string $apiBaseUrl;
    protected string $popBaseUrl;

    public function __construct()
    {
        $this->merchantCode = (string) config('duitku.merchant_code');
        $this->apiKey = (string) config('duitku.api_key');
        $this->apiBaseUrl = config('duitku.sandbox', true)
            ? 'https://sandbox.duitku.com/webapi/api'
            : 'https://passport.duitku.com/webapi/api';
        $this->popBaseUrl = config('duitku.sandbox', true)
            ? 'https://api-sandbox.duitku.com/api'
            : 'https://api-prod.duitku.com/api';
    }

    public function createCheckout(User $owner, string $planKey, array $plan, string $returnUrl): array
    {
        if (!$this->merchantCode || !$this->apiKey) {
            throw new \RuntimeException('Konfigurasi Duitku belum lengkap.');
        }

        $amount = (int) ($plan['price'] ?? 0);
        if ($amount <= 0) {
            throw new \RuntimeException('Harga paket belum tersedia untuk pembayaran.');
        }

        $merchantOrderId = $this->merchantOrderId($owner, $planKey);
        $paymentMethod = $this->resolvePaymentMethod($amount);
        $callbackUrl = (string) config('duitku.callback_url');
        $timestamp = (string) round(microtime(true) * 1000);
        $signature = hash('sha256', $this->merchantCode . $timestamp . $this->apiKey);

        $payload = [
            'paymentAmount' => $amount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $planKey === 'topup'
                ? 'Top-up kuota pesanan ShoeClean'
                : 'Langganan ' . ($plan['name'] ?? strtoupper($planKey)) . ' ShoeClean',
            'additionalParam' => '',
            'merchantUserInfo' => $owner->email,
            'customerVaName' => Str::limit($owner->name, 20, ''),
            'email' => $owner->email,
            'phoneNumber' => optional($owner->outlet)->phone ?? '',
            'itemDetails' => [
                [
                    'name' => $planKey === 'topup'
                        ? (($plan['name'] ?? 'Top-up') . ' ' . number_format((int) ($plan['quota'] ?? 0)) . ' pesanan')
                        : ('Langganan ' . ($plan['name'] ?? strtoupper($planKey))),
                    'price' => $amount,
                    'quantity' => 1,
                ],
            ],
            'customerDetail' => [
                'firstName' => $owner->name,
                'lastName' => '',
                'email' => $owner->email,
                'phoneNumber' => optional($owner->outlet)->phone ?? '',
            ],
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'expiryPeriod' => (int) config('duitku.expiry_period', 60),
        ];

        $response = Http::acceptJson()
            ->withHeaders([
                'x-duitku-signature' => $signature,
                'x-duitku-timestamp' => $timestamp,
                'x-duitku-merchantcode' => $this->merchantCode,
            ])
            ->post($this->popBaseUrl . '/merchant/createInvoice', $payload);

        if ($response->failed()) {
            Log::error('Duitku create checkout failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);

            throw new \RuntimeException('Gagal membuat link pembayaran Duitku.');
        }

        $data = $response->json();
        $data['merchantOrderId'] = $merchantOrderId;
        $data['paymentMethod'] = $paymentMethod;
        $data['productDetails'] = $payload['productDetails'];
        $data['popupScriptUrl'] = $this->popupScriptUrl();

        return $data;
    }

    public function popupScriptUrl(): string
    {
        return config('duitku.sandbox', true)
            ? 'https://app-sandbox.duitku.com/lib/js/duitku.js'
            : 'https://app-prod.duitku.com/lib/js/duitku.js';
    }

    public function getTransactionStatus(string $merchantOrderId): array
    {
        if (!$this->merchantCode || !$this->apiKey) {
            throw new \RuntimeException('Konfigurasi Duitku belum lengkap.');
        }

        $signature = md5($this->merchantCode . $merchantOrderId . $this->apiKey);

        $response = Http::acceptJson()->post($this->apiBaseUrl . '/merchant/transactionStatus', [
            'merchantCode' => $this->merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature' => $signature,
        ]);

        if ($response->failed()) {
            Log::error('Duitku transaction status failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'merchantOrderId' => $merchantOrderId,
            ]);

            throw new \RuntimeException('Gagal memeriksa status pembayaran Duitku.');
        }

        return $response->json();
    }

    public function isValidCallbackSignature(string $amount, string $merchantOrderId, string $signature): bool
    {
        $expected = md5($this->merchantCode . $amount . $merchantOrderId . $this->apiKey);

        return hash_equals($expected, $signature);
    }

    public function parseMerchantOrderId(string $merchantOrderId): array
    {
        $parts = explode('-', $merchantOrderId);

        return [
            'type' => strtolower($parts[0] ?? ''),
            'user_id' => isset($parts[1]) ? (int) str_replace('U', '', strtoupper($parts[1])) : null,
        ];
    }

    protected function merchantOrderId(User $owner, string $planKey): string
    {
        return strtoupper($planKey) . '-U' . $owner->id . '-' . now()->format('ymdHis') . '-' . strtoupper(Str::random(5));
    }

    protected function resolvePaymentMethod(int $amount): string
    {
        $configured = trim((string) config('duitku.default_payment_method'));
        if ($configured !== '') {
            return strtoupper($configured);
        }

        $methods = $this->getPaymentMethods($amount);
        if (empty($methods)) {
            throw new \RuntimeException('Metode pembayaran Duitku tidak tersedia.');
        }

        $preferred = array_filter(array_map(
            static fn ($item) => strtoupper(trim($item)),
            explode(',', (string) config('duitku.preferred_payment_methods', '')),
        ));

        foreach ($preferred as $preferredCode) {
            foreach ($methods as $method) {
                if (($method['paymentMethod'] ?? null) === $preferredCode) {
                    return $preferredCode;
                }
            }
        }

        return (string) ($methods[0]['paymentMethod'] ?? '');
    }

    protected function getPaymentMethods(int $amount): array
    {
        $datetime = now()->format('Y-m-d H:i:s');
        $signature = hash('sha256', $this->merchantCode . $amount . $datetime . $this->apiKey);

        $response = Http::acceptJson()->post($this->apiBaseUrl . '/merchant/paymentmethod/getpaymentmethod', [
            'merchantcode' => $this->merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature,
        ]);

        if ($response->failed()) {
            Log::error('Duitku payment method lookup failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Gagal mengambil metode pembayaran Duitku.');
        }

        return $response->json('paymentFee', []);
    }
}
