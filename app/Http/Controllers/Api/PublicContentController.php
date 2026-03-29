<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Service;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;

class PublicContentController extends Controller
{
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
                'tagline' => 'Operasional outlet lebih rapi',
            ],
            'hero' => [
                'badge' => 'Operasional Outlet Lebih Rapi',
                'title' => 'Kelola order, pembayaran, dan outlet lebih mudah.',
                'description' => 'Web app untuk bisnis shoe care dan laundry yang ingin order lebih tertata, customer bisa tracking sendiri, pembayaran QRIS lebih rapi, dan owner punya laporan yang siap dipakai setiap hari.',
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
                    'title' => 'Manajemen Multi-Cabang',
                    'description' => 'Kelola satu atau banyak cabang dengan alur yang konsisten, termasuk QRIS per outlet dan kontrol owner lintas outlet.',
                ],
                [
                    'title' => 'Order & Tracking',
                    'description' => 'Customer bisa order lebih rapi, tim outlet lebih mudah memproses, dan status pesanan bisa dicek kapan saja lewat invoice.',
                ],
                [
                    'title' => 'Pembayaran & Laporan',
                    'description' => 'QRIS outlet, verifikasi bukti bayar, export laporan, dan insight bisnis untuk membantu owner mengambil keputusan lebih cepat.',
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
                'title' => 'Pilih paket sesuai tahap bisnis Anda.',
                'description' => 'Mulai dari Free untuk mencoba alur operasional, lanjut ke Pro untuk 1 outlet yang sudah aktif, atau gunakan Business saat Anda mulai mengelola banyak cabang.',
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
                'pickup_fee' => (int) ($outlet->pickup_fee ?? 0),
                'delivery_fee' => (int) ($outlet->delivery_fee ?? 0),
                'has_qris' => filled($outlet->qris_image_path),
                'services_count' => (int) $outlet->services_count,
            ])
            ->values();
    }
}
