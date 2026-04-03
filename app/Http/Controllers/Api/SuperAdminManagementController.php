<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Services\PricingCatalogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminManagementController
{
    public function dashboard(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $month = max(1, min(12, (int) $request->integer('month', now()->month)));
        $year = (int) $request->integer('year', now()->year);

        $availableYears = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->values()
            ->all();

        if (!in_array(now()->year, $availableYears, true)) {
            array_unshift($availableYears, now()->year);
        }

        $totalOutlets = Outlet::count();
        $activeOutlets = Outlet::where('status', 'active')->count();
        $totalOwners = User::whereHas('roles', fn ($query) => $query->where('slug', 'owner'))->count();
        $totalUsers = User::whereHas('roles', fn ($query) => $query->whereIn('slug', ['owner', 'admin', 'staff']))->count();

        $outlets = Outlet::query()
            ->withCount('orders')
            ->with('owner:id,name')
            ->get()
            ->map(function (Outlet $outlet) use ($month, $year) {
                $outlet->revenue = Payment::where('status', 'success')
                    ->whereHas('order', fn ($query) => $query->where('outlet_id', $outlet->id))
                    ->sum('amount');

                $outlet->month_orders = Order::where('outlet_id', $outlet->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count();

                $outlet->month_revenue = Payment::where('status', 'success')
                    ->whereHas('order', fn ($query) => $query->where('outlet_id', $outlet->id))
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('amount');

                return $outlet;
            })
            ->sortByDesc('revenue')
            ->values();

        $revenueTrend = collect(range(5, 0))
            ->map(function (int $index) {
                $date = Carbon::now()->subMonths($index);

                return [
                    'label' => $date->format('M Y'),
                    'value' => (int) Payment::where('status', 'success')
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('amount'),
                ];
            })
            ->values();

        $outletGrowth = collect(range(5, 0))
            ->map(function (int $index) {
                $date = Carbon::now()->subMonths($index);

                return [
                    'label' => $date->format('M Y'),
                    'value' => (int) Outlet::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count(),
                ];
            })
            ->values();

        $recentOrders = Order::query()
            ->with(['customer:id,name,phone', 'outlet:id,name,slug'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Order $order) => $this->transformOrder($order))
            ->values();

        return response()->json([
            'filters' => [
                'month' => $month,
                'year' => $year,
                'available_years' => $availableYears,
            ],
            'metrics' => [
                'total_outlets' => $totalOutlets,
                'active_outlets' => $activeOutlets,
                'inactive_outlets' => max(0, $totalOutlets - $activeOutlets),
                'total_owners' => $totalOwners,
                'total_users' => $totalUsers,
                'total_customers' => Customer::count(),
                'total_services' => Service::count(),
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'month_orders' => Order::whereMonth('created_at', $month)->whereYear('created_at', $year)->count(),
                'total_orders' => Order::count(),
                'today_revenue' => (int) Payment::where('status', 'success')->whereDate('created_at', today())->sum('amount'),
                'month_revenue' => (int) Payment::where('status', 'success')->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('amount'),
                'total_revenue' => (int) Payment::where('status', 'success')->sum('amount'),
            ],
            'charts' => [
                'revenue' => [
                    'labels' => $revenueTrend->pluck('label')->all(),
                    'data' => $revenueTrend->pluck('value')->all(),
                ],
                'growth' => [
                    'labels' => $outletGrowth->pluck('label')->all(),
                    'data' => $outletGrowth->pluck('value')->all(),
                ],
            ],
            'outlets' => $outlets->map(fn (Outlet $outlet, int $index) => [
                'id' => $outlet->id,
                'rank' => $index + 1,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
                'city_name' => $outlet->city_name,
                'address' => $outlet->address,
                'status' => $outlet->status ?? 'active',
                'owner_name' => $outlet->owner?->name,
                'month_orders' => (int) $outlet->month_orders,
                'month_revenue' => (int) $outlet->month_revenue,
                'total_orders' => (int) $outlet->orders_count,
                'total_revenue' => (int) $outlet->revenue,
            ])->values(),
            'recent_orders' => $recentOrders,
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $query = Order::query()
            ->with(['customer:id,name,phone', 'outlet:id,name,slug,owner_id', 'outlet.owner:id,name']);

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', fn ($inner) => $inner->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('outlet', fn ($inner) => $inner->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('outlet.owner', fn ($inner) => $inner->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($outletId = $request->integer('outlet_id')) {
            $query->where('outlet_id', $outletId);
        }

        if ($ownerId = $request->integer('owner_id')) {
            $query->whereHas('outlet', fn ($builder) => $builder->where('owner_id', $ownerId));
        }

        if ($status = trim((string) $request->string('status'))) {
            $query->where('status', $status);
        }

        if ($paymentStatus = trim((string) $request->string('payment_status'))) {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query
            ->latest()
            ->paginate(12)
            ->through(fn (Order $order) => $this->transformOrder($order, true));

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'outlet_id' => $outletId ?: null,
                'owner_id' => $ownerId ?: null,
                'status' => $status ?? '',
                'payment_status' => $paymentStatus ?? '',
            ],
            'outlets' => Outlet::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn (Outlet $outlet) => [
                    'id' => $outlet->id,
                    'name' => $outlet->name,
                    'slug' => $outlet->slug,
                ])
                ->values(),
            'owners' => User::query()
                ->whereHas('roles', fn ($builder) => $builder->where('slug', 'owner'))
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (User $owner) => [
                    'id' => $owner->id,
                    'name' => $owner->name,
                ])
                ->values(),
            'statuses' => ['pending', 'processing', 'ready', 'completed', 'picked_up', 'cancelled'],
            'payment_statuses' => ['unpaid', 'waiting_confirmation', 'paid'],
            'orders' => $orders,
        ]);
    }

    public function subscriptions(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $planFilter = trim((string) $request->string('plan'));
        $statusFilter = trim((string) $request->string('status'));

        $owners = User::query()
            ->whereHas('roles', fn ($builder) => $builder->where('slug', 'owner'))
            ->with(['subscriptions' => fn ($builder) => $builder->latest('started_at')])
            ->get();

        $expiringSubscriptions = Subscription::query()
            ->with('user:id,name,email')
            ->active()
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->orderBy('expires_at')
            ->limit(10)
            ->get()
            ->map(fn (Subscription $subscription) => $this->transformSubscription($subscription))
            ->values();

        $subscriptions = Subscription::query()
            ->with('user:id,name,email')
            ->when($planFilter, fn ($builder) => $builder->where('plan', $planFilter))
            ->when($statusFilter === 'active', fn ($builder) => $builder->active())
            ->when($statusFilter === 'expired', fn ($builder) => $builder->expired())
            ->latest('started_at')
            ->paginate(12)
            ->through(fn (Subscription $subscription) => $this->transformSubscription($subscription));

        return response()->json([
            'summary' => [
                'free_owners' => $owners->filter(fn (User $owner) => $owner->currentPlan() === 'free')->count(),
                'pro_owners' => $owners->filter(fn (User $owner) => $owner->currentPlan() === 'pro')->count(),
                'business_owners' => $owners->filter(fn (User $owner) => $owner->currentPlan() === 'business')->count(),
                'expiring_count' => $expiringSubscriptions->count(),
            ],
            'filters' => [
                'plan' => $planFilter,
                'status' => $statusFilter,
            ],
            'expiring' => $expiringSubscriptions,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function pricingIndex(Request $request, PricingCatalogService $pricingCatalogService): JsonResponse
    {
        $this->authorizeSuperAdmin($request);
        $pricingCatalogService->syncDefaults();

        return response()->json([
            'plans' => PricingPlan::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (PricingPlan $plan) => $this->transformPricingPlan($plan))
                ->values(),
        ]);
    }

    public function pricingStore(Request $request, PricingCatalogService $pricingCatalogService): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', 'unique:pricing_plans,key'],
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'cta' => ['nullable', 'string', 'max:255'],
            'order_limit' => ['nullable', 'integer', 'min:0'],
            'max_outlets' => ['nullable', 'integer', 'min:0'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
        ]);

        $plan = PricingPlan::create($this->normalizePricingPayload($validated));
        $pricingCatalogService->syncDefaults();

        return response()->json([
            'message' => 'Harga berhasil ditambahkan.',
            'plan' => $this->transformPricingPlan($plan->refresh()),
        ], 201);
    }

    public function pricingUpdate(
        Request $request,
        PricingPlan $plan,
        PricingCatalogService $pricingCatalogService,
    ): JsonResponse {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:50', 'unique:pricing_plans,key,' . $plan->id],
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'cta' => ['nullable', 'string', 'max:255'],
            'order_limit' => ['nullable', 'integer', 'min:0'],
            'max_outlets' => ['nullable', 'integer', 'min:0'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
        ]);

        $plan->update($this->normalizePricingPayload($validated));
        $pricingCatalogService->syncDefaults();

        return response()->json([
            'message' => 'Harga berhasil diperbarui.',
            'plan' => $this->transformPricingPlan($plan->refresh()),
        ]);
    }

    public function pricingDestroy(Request $request, PricingPlan $plan): JsonResponse
    {
        $this->authorizeSuperAdmin($request);
        $plan->delete();

        return response()->json([
            'message' => 'Harga berhasil dihapus.',
        ]);
    }

    public function payments(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $search = trim((string) $request->string('search'));
        $kind = trim((string) $request->string('kind'));
        $status = trim((string) $request->string('status'));
        $ownerId = $request->integer('owner_id');

        $transactions = PaymentTransaction::query()
            ->with(['user:id,name,email', 'billable'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('merchant_order_id', 'like', '%' . $search . '%')
                        ->orWhere('reference', 'like', '%' . $search . '%')
                        ->orWhere('customer_email', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($kind, fn ($query) => $query->where('kind', $kind))
            ->when($status, fn ($query) => $query->where('status_code', $status))
            ->when($ownerId, fn ($query) => $query->where('user_id', $ownerId))
            ->latest()
            ->paginate(12)
            ->through(fn (PaymentTransaction $transaction) => $this->transformTransaction($transaction));

        return response()->json([
            'summary' => [
                'total' => PaymentTransaction::count(),
                'success' => PaymentTransaction::where('status_code', '00')->count(),
                'pending' => PaymentTransaction::where('status_code', '01')->whereNull('paid_at')->count(),
                'failed' => PaymentTransaction::whereIn('status_code', ['02', '03'])->count(),
            ],
            'filters' => [
                'search' => $search,
                'kind' => $kind,
                'status' => $status,
                'owner_id' => $ownerId ?: null,
            ],
            'owners' => User::query()
                ->whereHas('roles', fn ($builder) => $builder->where('slug', 'owner'))
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (User $owner) => ['id' => $owner->id, 'name' => $owner->name])
                ->values(),
            'transactions' => $transactions,
        ]);
    }

    public function feedbacks(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $category = trim((string) $request->string('category'));

        $feedbacks = Feedback::query()
            ->with(['user:id,name', 'outlet:id,name'])
            ->when($category, fn ($builder) => $builder->where('category', $category))
            ->latest()
            ->paginate(12)
            ->through(fn (Feedback $feedback) => [
                'id' => $feedback->id,
                'category' => $feedback->category,
                'category_label' => match ($feedback->category) {
                    'keluhan' => 'Keluhan',
                    'ide' => 'Ide',
                    'saran' => 'Saran',
                    default => ucfirst((string) $feedback->category),
                },
                'message' => $feedback->message,
                'created_at' => optional($feedback->created_at)->toIso8601String(),
                'user' => $feedback->user ? ['name' => $feedback->user->name] : null,
                'outlet' => $feedback->outlet ? ['name' => $feedback->outlet->name] : null,
            ]);

        return response()->json([
            'filters' => [
                'category' => $category,
            ],
            'summary' => [
                'total' => Feedback::count(),
                'saran' => Feedback::where('category', 'saran')->count(),
                'ide' => Feedback::where('category', 'ide')->count(),
                'keluhan' => Feedback::where('category', 'keluhan')->count(),
            ],
            'categories' => ['saran', 'ide', 'keluhan'],
            'feedbacks' => $feedbacks,
        ]);
    }

    public function surveys(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $status = trim((string) $request->string('status'));
        $search = trim((string) $request->string('search'));

        $query = Survey::query()
            ->platform()
            ->withCount('responses')
            ->with('creator:id,name')
            ->latest();

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $surveys = $query
            ->paginate(12)
            ->through(fn (Survey $survey) => $this->transformSurvey($survey));

        return response()->json([
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'summary' => [
                'total_surveys' => Survey::platform()->count(),
                'active_surveys' => Survey::platform()->where('is_active', true)->count(),
                'total_responses' => Survey::platform()->withCount('responses')->get()->sum('responses_count'),
            ],
            'statuses' => ['active', 'inactive'],
            'surveys' => $surveys,
        ]);
    }

    public function surveyShow(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeSuperAdmin($request);
        abort_unless($survey->type === 'platform', 403);

        $survey->load(['questions', 'creator:id,name']);
        $responses = $survey->responses()->with('answers.question')->latest()->get();

        return response()->json([
            'survey' => $this->transformSurvey($survey, true),
            'question_stats' => $survey->questions
                ->map(fn (SurveyQuestion $question) => $this->buildQuestionStats($question, $responses))
                ->values(),
            'recent_responses' => $responses->take(12)->map(fn (SurveyResponse $response) => [
                'id' => $response->id,
                'respondent_name' => $response->respondent_name,
                'respondent_phone' => $response->respondent_phone,
                'respondent_type' => $response->respondent_type,
                'created_at' => optional($response->created_at)->toIso8601String(),
                'answers' => $response->answers->map(fn (SurveyAnswer $answer) => [
                    'question' => $answer->question?->question,
                    'type' => $answer->question?->type,
                    'answer' => $answer->answer,
                    'rating' => $answer->rating,
                ])->values(),
            ])->values(),
        ]);
    }

    public function surveyStore(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string', 'max:500'],
            'questions.*.type' => ['required', 'in:rating,text,choice'],
            'questions.*.options' => ['nullable', 'array'],
        ]);

        foreach ($validated['questions'] as $index => $question) {
            if ($question['type'] !== 'choice') {
                continue;
            }

            $options = array_values(array_filter(
                $question['options'] ?? [],
                fn ($option) => trim((string) $option) !== '',
            ));

            if (count($options) < 2) {
                return response()->json([
                    'message' => 'Pilihan ganda minimal 2 opsi.',
                    'errors' => [
                        "questions.$index.options" => ['Pilihan ganda minimal 2 opsi.'],
                    ],
                ], 422);
            }
        }

        $survey = DB::transaction(function () use ($validated, $request) {
            $survey = Survey::create([
                'type' => 'platform',
                'outlet_id' => null,
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(6)),
                'description' => $validated['description'] ?? null,
                'is_active' => true,
                'created_by' => $request->user()->id,
            ]);

            foreach ($validated['questions'] as $index => $question) {
                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'options' => $question['type'] === 'choice'
                        ? array_values(array_filter($question['options'] ?? [], fn ($option) => trim((string) $option) !== ''))
                        : null,
                    'sort_order' => $index,
                ]);
            }

            return $survey;
        });

        $survey->load(['questions', 'creator:id,name'])->loadCount('responses');

        return response()->json([
            'message' => 'Survey platform berhasil dibuat.',
            'survey' => $this->transformSurvey($survey, true),
        ], 201);
    }

    public function surveyToggle(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeSuperAdmin($request);
        abort_unless($survey->type === 'platform', 403);

        $survey->update(['is_active' => !$survey->is_active]);
        $survey->load(['creator:id,name'])->loadCount('responses');

        return response()->json([
            'message' => $survey->is_active ? 'Survey diaktifkan.' : 'Survey dinonaktifkan.',
            'survey' => $this->transformSurvey($survey),
        ]);
    }

    public function surveyDestroy(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeSuperAdmin($request);
        abort_unless($survey->type === 'platform', 403);

        $survey->delete();

        return response()->json([
            'message' => 'Survey berhasil dihapus.',
        ]);
    }

    protected function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Akses superadmin tidak tersedia untuk akun ini.');
    }

    protected function normalizePricingPayload(array $payload): array
    {
        return [
            'key' => strtolower(trim((string) $payload['key'])),
            'name' => $payload['name'],
            'subtitle' => $payload['subtitle'] ?? null,
            'price' => (int) ($payload['price'] ?? 0),
            'description' => $payload['description'] ?? null,
            'cta' => $payload['cta'] ?? null,
            'order_limit' => $payload['order_limit'] ?? null,
            'max_outlets' => $payload['max_outlets'] ?? null,
            'quota' => $payload['quota'] ?? null,
            'is_published' => (bool) ($payload['is_published'] ?? false),
            'sort_order' => (int) ($payload['sort_order'] ?? 10),
            'features' => collect($payload['features'] ?? [])
                ->map(fn ($feature) => trim((string) $feature))
                ->filter()
                ->values()
                ->all(),
        ];
    }

    protected function transformPricingPlan(PricingPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'key' => $plan->key,
            'name' => $plan->name,
            'subtitle' => $plan->subtitle,
            'price' => (int) $plan->price,
            'price_label' => 'Rp' . number_format((int) $plan->price, 0, ',', '.'),
            'description' => $plan->description,
            'cta' => $plan->cta,
            'order_limit' => $plan->order_limit,
            'max_outlets' => $plan->max_outlets,
            'quota' => $plan->quota,
            'features' => $plan->features ?? [],
            'is_published' => (bool) $plan->is_published,
            'sort_order' => (int) $plan->sort_order,
            'created_at' => optional($plan->created_at)->toIso8601String(),
        ];
    }

    protected function transformOrder(Order $order, bool $includeOwner = false): array
    {
        return [
            'id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'status' => $order->status,
            'status_label' => ucwords(str_replace('_', ' ', (string) $order->status)),
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->paymentStatusLabel(),
            'payment_method' => $order->paymentMethodLabel(),
            'total_price' => (int) $order->total_price,
            'created_at' => optional($order->created_at)->toIso8601String(),
            'customer' => $order->customer ? [
                'name' => $order->customer->name,
                'phone' => $order->customer->phone,
            ] : null,
            'outlet' => $order->outlet ? [
                'id' => $order->outlet->id,
                'name' => $order->outlet->name,
                'slug' => $order->outlet->slug,
            ] : null,
            'owner' => $includeOwner && $order->outlet?->owner ? [
                'id' => $order->outlet->owner->id,
                'name' => $order->outlet->owner->name,
            ] : null,
        ];
    }

    protected function transformSubscription(Subscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'plan' => $subscription->plan,
            'status' => $subscription->isActive() ? 'active' : 'expired',
            'status_label' => $subscription->isActive() ? 'Aktif' : 'Berakhir',
            'started_at' => optional($subscription->started_at)->toIso8601String(),
            'expires_at' => optional($subscription->expires_at)->toIso8601String(),
            'days_remaining' => $subscription->daysRemaining(),
            'user' => $subscription->user ? [
                'id' => $subscription->user->id,
                'name' => $subscription->user->name,
                'email' => $subscription->user->email,
            ] : null,
        ];
    }

    protected function transformTransaction(PaymentTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'gateway' => $transaction->gateway,
            'kind' => $transaction->kind,
            'kind_label' => $transaction->kind === 'subscription' ? 'Langganan' : 'Top Up',
            'plan_key' => $transaction->plan_key,
            'merchant_order_id' => $transaction->merchant_order_id,
            'reference' => $transaction->reference,
            'payment_method' => $transaction->payment_method,
            'amount' => (int) $transaction->amount,
            'fee' => $transaction->fee !== null ? (float) $transaction->fee : null,
            'status_code' => $transaction->status_code,
            'status_message' => $transaction->status_message,
            'product_detail' => $transaction->product_detail,
            'customer_email' => $transaction->customer_email,
            'checkout_payload' => $transaction->checkout_payload,
            'callback_payload' => $transaction->callback_payload,
            'status_payload' => $transaction->status_payload,
            'paid_at' => optional($transaction->paid_at)->toIso8601String(),
            'expires_at' => optional($transaction->expires_at)->toIso8601String(),
            'last_synced_at' => optional($transaction->last_synced_at)->toIso8601String(),
            'billable' => $transaction->billable_type
                ? [
                    'type' => class_basename($transaction->billable_type),
                    'id' => $transaction->billable_id,
                ]
                : null,
            'user' => $transaction->user ? [
                'id' => $transaction->user->id,
                'name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ] : null,
        ];
    }

    protected function transformSurvey(Survey $survey, bool $detailed = false): array
    {
        $payload = [
            'id' => $survey->id,
            'title' => $survey->title,
            'slug' => $survey->slug,
            'description' => $survey->description,
            'type' => $survey->type,
            'is_active' => (bool) $survey->is_active,
            'responses_count' => (int) ($survey->responses_count ?? $survey->responses()->count()),
            'average_rating' => $survey->averageRating(),
            'public_url' => url("/survey/{$survey->slug}"),
            'created_at' => optional($survey->created_at)->toIso8601String(),
            'creator' => $survey->creator ? [
                'id' => $survey->creator->id,
                'name' => $survey->creator->name,
            ] : null,
        ];

        if ($detailed) {
            $payload['questions'] = $survey->questions->map(fn (SurveyQuestion $question) => [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'options' => $question->options ?? [],
                'sort_order' => $question->sort_order,
            ])->values();
        }

        return $payload;
    }

    protected function buildQuestionStats(SurveyQuestion $question, $responses): array
    {
        $answers = $responses
            ->flatMap->answers
            ->where('survey_question_id', $question->id);

        $stat = [
            'id' => $question->id,
            'question' => $question->question,
            'type' => $question->type,
        ];

        if ($question->type === 'rating') {
            $ratings = $answers->pluck('rating')->filter();
            $stat['average'] = $ratings->count() > 0 ? round($ratings->avg(), 1) : 0;
            $stat['count'] = $ratings->count();
            $stat['distribution'] = collect(range(1, 5))
                ->mapWithKeys(fn ($rating) => [(string) $rating => $ratings->filter(fn ($item) => (int) $item === $rating)->count()])
                ->all();
        } elseif ($question->type === 'choice') {
            $options = $question->options ?? [];
            $stat['options'] = $options;
            $stat['distribution'] = collect($options)
                ->mapWithKeys(fn ($option) => [$option => $answers->where('answer', $option)->count()])
                ->all();
        } else {
            $stat['answers'] = $answers
                ->pluck('answer')
                ->filter(fn ($answer) => filled($answer))
                ->values()
                ->take(20)
                ->all();
        }

        return $stat;
    }
}
