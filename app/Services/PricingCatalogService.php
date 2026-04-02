<?php

namespace App\Services;

use App\Models\PricingPlan;
use Illuminate\Support\Facades\Schema;

class PricingCatalogService
{
    public function defaultPlans(): array
    {
        return [
            'free' => [
                'key' => 'free',
                'name' => 'Free',
                'subtitle' => 'Untuk mulai operasional',
                'price' => 0,
                'order_limit' => config('mayar.plans.free.order_limit', 100),
                'max_outlets' => config('mayar.plans.free.max_outlets', 1),
                'quota' => null,
                'description' => 'Cocok untuk toko baru yang ingin mulai mencatat pesanan dan mencoba alur digital.',
                'cta' => 'Mulai Gratis',
                'features' => [
                    '1 cabang',
                    '100 pesanan total',
                    'Lacak pesanan pelanggan',
                    'QRIS cabang & pembayaran manual',
                    'Laporan dasar',
                ],
                'is_published' => true,
                'sort_order' => 10,
            ],
            'pro' => [
                'key' => 'pro',
                'name' => 'Pro',
                'subtitle' => 'Untuk 1 outlet yang sudah aktif',
                'price' => config('mayar.plans.pro.price', 75000),
                'order_limit' => null,
                'max_outlets' => config('mayar.plans.pro.max_outlets', 1),
                'quota' => null,
                'description' => 'Paket terbaik untuk satu cabang yang ingin operasional tanpa batas dan fitur bisnis yang lebih lengkap.',
                'cta' => 'Upgrade ke Pro',
                'features' => [
                    '1 cabang',
                    'Pesanan tanpa batas',
                    'Promo & voucher',
                    'Ekspor laporan PDF/Excel',
                    'Kelola admin dan staf cabang',
                ],
                'is_published' => true,
                'sort_order' => 20,
            ],
            'business' => [
                'key' => 'business',
                'name' => 'Business',
                'subtitle' => 'Untuk bisnis multi cabang',
                'price' => config('mayar.plans.business.price', 200000),
                'order_limit' => null,
                'max_outlets' => config('mayar.plans.business.max_outlets'),
                'quota' => null,
                'description' => 'Untuk owner yang mengelola banyak cabang dan butuh kontrol operasional serta laporan lintas cabang.',
                'cta' => 'Upgrade ke Business',
                'features' => [
                    'Semua fitur Pro',
                    'Cabang tanpa batas',
                    'Pesanan tanpa batas',
                    'Laporan multi cabang',
                    'Kontrol bisnis lintas cabang',
                ],
                'is_published' => true,
                'sort_order' => 30,
            ],
            'topup' => [
                'key' => 'topup',
                'name' => 'Top-up Kuota',
                'subtitle' => 'Tambahan untuk paket Free',
                'price' => config('mayar.topup.price', 100000),
                'order_limit' => null,
                'max_outlets' => null,
                'quota' => config('mayar.topup.quota', 500),
                'description' => 'Saat kuota gratis habis, Anda bisa menambah kuota pesanan tanpa harus langsung upgrade paket.',
                'cta' => 'Beli Top-up',
                'features' => [],
                'is_published' => true,
                'sort_order' => 40,
            ],
        ];
    }

    public function syncDefaults(): void
    {
        if (!Schema::hasTable('pricing_plans')) {
            return;
        }

        foreach ($this->defaultPlans() as $default) {
            PricingPlan::firstOrCreate(
                ['key' => $default['key']],
                $default,
            );
        }
    }

    public function getPlanDetails(): array
    {
        $defaults = $this->defaultPlans();
        if (!Schema::hasTable('pricing_plans')) {
            return collect($defaults)
                ->map(function (array $default, string $key) {
                    $default['features'] = array_values($default['features'] ?? []);
                    $default['price_label'] = $this->formatPriceLabel(
                        (int) ($default['price'] ?? 0),
                        $key,
                        (bool) ($default['is_published'] ?? false),
                    );

                    return $default;
                })
                ->all();
        }

        $records = PricingPlan::query()->get()->keyBy('key');

        $plans = [];

        foreach ($defaults as $key => $default) {
            /** @var PricingPlan|null $record */
            $record = $records->get($key);

            $merged = array_merge($default, $record?->only([
                'name',
                'subtitle',
                'price',
                'order_limit',
                'max_outlets',
                'quota',
                'description',
                'cta',
                'features',
                'is_published',
                'sort_order',
            ]) ?? []);

            $merged['features'] = array_values($merged['features'] ?? []);
            $merged['price_label'] = $this->formatPriceLabel(
                (int) ($merged['price'] ?? 0),
                $key,
                (bool) ($merged['is_published'] ?? false),
            );

            $plans[$key] = $merged;
        }

        return $plans;
    }

    public function getPlan(string $key): ?array
    {
        return $this->getPlanDetails()[$key] ?? null;
    }

    public function formatPriceLabel(int $price, string $key, bool $published): string
    {
        if (!$published) {
            return 'Coming Soon';
        }

        if ($price === 0) {
            return 'Gratis';
        }

        $formatted = 'Rp' . number_format($price, 0, ',', '.');

        return $key === 'topup' ? $formatted : $formatted . '/bulan';
    }
}
