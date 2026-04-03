<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Service;
use App\Services\DistancePricingService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;

class PublicContentController extends Controller
{
    public function __construct(protected DistancePricingService $distancePricing)
    {
    }

    public function home(): JsonResponse
    {
        $services = Service::query()
            ->active()
            ->with('outlet:id,name,slug')
            ->orderBy('name')
            ->take(4)
            ->get()
            ->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'unit' => $service->unit,
                'price' => (int) $service->price,
                'outlet' => $service->outlet ? [
                    'id' => $service->outlet->id,
                    'name' => $service->outlet->name,
                    'slug' => $service->outlet->slug,
                ] : null,
            ])
            ->values();

        return response()->json([
            'meta' => [
                'product_name' => 'ShoeClean',
                'tagline' => 'Kurangi chat manual, rapikan operasional outlet',
            ],
            'hero' => [
                'badge' => 'Kurangi Chat Manual, Rapikan Operasional',
                'title' => 'Software operasional untuk shoe care yang ingin tumbuh.',
                'description' => 'Catat order lebih rapi, biarkan customer cek status sendiri, dan bantu owner memantau omzet serta performa outlet tanpa rekap manual yang melelahkan.',
                'primary_cta' => [
                    'label' => 'Mulai Gratis',
                    'href' => '/register',
                ],
                'secondary_cta' => [
                    'label' => 'Lihat Paket',
                    'href' => '/pricing',
                ],
            ],
            'features' => [
                [
                    'title' => 'Kontrol Multi-Cabang',
                    'description' => 'Owner bisa mengawasi satu atau banyak cabang dengan alur kerja yang sama, tanpa rekap manual yang memakan waktu.',
                ],
                [
                    'title' => 'Order Lebih Rapi',
                    'description' => 'Pesanan masuk lebih tertata, tim outlet lebih mudah proses, dan customer bisa cek status sendiri tanpa bolak-balik chat admin.',
                ],
                [
                    'title' => 'Pembayaran & Insight',
                    'description' => 'QRIS per outlet, verifikasi pembayaran, dan laporan yang membantu owner melihat omzet serta performa cabang lebih cepat.',
                ],
            ],
            'services' => $services,
            'outlets' => $this->publicOutlets(),
        ]);
    }

    public function pricing(SubscriptionService $subscriptionService): JsonResponse
    {
        return response()->json([
            'meta' => [
                'title' => 'Harga ShoeClean',
                'description' => 'Paket Free, Pro, dan Business untuk operasional shoe care dan laundry.',
            ],
            'hero' => [
                'badge' => 'Pricing',
                'title' => 'Pilih paket yang membuat operasional lebih untung.',
                'description' => 'Mulai dari Free untuk trial, lanjut ke Pro saat satu outlet Anda sudah sibuk, dan gunakan Business ketika owner perlu kontrol cabang serta laporan gabungan tanpa rekap manual.',
            ],
            'plans' => $subscriptionService->getPlanDetails(),
            'outlets' => $this->publicOutlets(),
        ]);
    }

    protected function publicOutlets()
    {
        return Outlet::query()
            ->where('status', 'active')
            ->withCount('services')
            ->orderBy('name')
            ->take(6)
            ->get()
            ->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
                'address' => $outlet->address,
                'phone' => $outlet->phone,
                'pickup_enabled' => (bool) $outlet->pickup_enabled,
                'delivery_enabled' => (bool) $outlet->delivery_enabled,
                'pickup_fee' => (int) ($outlet->pickup_fee ?? 0),
                'delivery_fee' => (int) ($outlet->delivery_fee ?? 0),
                'pickup_pricing' => $this->distancePricing->calculatePickup($outlet, null, null),
                'delivery_pricing' => $this->distancePricing->calculateDelivery($outlet, null, null),
                'has_qris' => filled($outlet->qris_image_path),
                'services_count' => (int) $outlet->services_count,
            ])
            ->values();
    }
}
