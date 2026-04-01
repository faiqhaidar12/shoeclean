<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseManagementController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $allowedOutletIds = $this->visibleOutletIds($request);
        $selectedOutletId = $request->integer('outlet_id') ?: null;
        $month = max(1, min(12, $request->integer('month') ?: now()->month));
        $year = max(2020, min(2100, $request->integer('year') ?: now()->year));

        $query = Expense::query()
            ->with(['outlet:id,name,slug', 'user:id,name'])
            ->whereIn('outlet_id', $allowedOutletIds)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->latest('expense_date')
            ->latest('id');

        if ($selectedOutletId && in_array($selectedOutletId, $allowedOutletIds, true)) {
            $query->where('outlet_id', $selectedOutletId);
        } elseif ($user->isOwner() && session('current_outlet_id')) {
            $sessionOutletId = (int) session('current_outlet_id');
            if (in_array($sessionOutletId, $allowedOutletIds, true)) {
                $query->where('outlet_id', $sessionOutletId);
                $selectedOutletId = $sessionOutletId;
            }
        }

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('category', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $totalAmount = (clone $query)->sum('amount');

        $expenses = $query
            ->paginate(12)
            ->through(fn (Expense $expense) => $this->transformExpense($expense));

        $years = Expense::query()
            ->whereIn('outlet_id', $allowedOutletIds)
            ->selectRaw('YEAR(expense_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($item) => (int) $item)
            ->values()
            ->all();

        if (!in_array((int) now()->year, $years, true)) {
            array_unshift($years, (int) now()->year);
        }

        return response()->json([
            'filters' => [
                'search' => (string) $request->string('search'),
                'month' => $month,
                'year' => $year,
                'outlet_id' => $selectedOutletId ?: null,
            ],
            'summary' => [
                'total_amount' => $totalAmount,
                'categories_count' => count(array_unique(array_map(
                    fn (array $expense) => $expense['category'],
                    $expenses->items()
                ))),
            ],
            'outlets' => $this->formOutlets($request),
            'categories' => $this->categories(),
            'available_years' => $years,
            'expenses' => $expenses,
            'meta' => [
                'can_choose_outlet' => $user->isOwner() || count($allowedOutletIds) > 1,
            ],
        ]);
    }

    public function show(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);
        $expense->load(['outlet:id,name,slug', 'user:id,name']);

        return response()->json([
            'expense' => $this->transformExpense($expense, true),
            'outlets' => $this->formOutlets($request),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'expense_date' => ['required', 'date'],
        ]);

        $validated['outlet_id'] = $this->sanitizeOutletId($request, (int) $validated['outlet_id']);
        $validated['user_id'] = (int) $request->user()->id;

        $expense = Expense::create($validated);
        $expense->load(['outlet:id,name,slug', 'user:id,name']);

        return response()->json([
            'message' => 'Pengeluaran berhasil dicatat.',
            'expense' => $this->transformExpense($expense, true),
        ], 201);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $this->authorizeExpense($request, $expense);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'expense_date' => ['required', 'date'],
        ]);

        $expense->update($validated);
        $expense->load(['outlet:id,name,slug', 'user:id,name']);

        return response()->json([
            'message' => 'Pengeluaran berhasil diperbarui.',
            'expense' => $this->transformExpense($expense, true),
        ]);
    }

    protected function transformExpense(Expense $expense, bool $detailed = false): array
    {
        $base = [
            'id' => $expense->id,
            'category' => $expense->category,
            'amount' => $expense->amount,
            'description' => $expense->description,
            'expense_date' => optional($expense->expense_date)->format('Y-m-d'),
            'created_at' => optional($expense->created_at)->toIso8601String(),
            'outlet' => $expense->outlet ? [
                'id' => $expense->outlet->id,
                'name' => $expense->outlet->name,
                'slug' => $expense->outlet->slug,
            ] : null,
            'user' => $expense->user ? [
                'id' => $expense->user->id,
                'name' => $expense->user->name,
            ] : null,
        ];

        if ($detailed) {
            $base['outlet_id'] = $expense->outlet_id;
            $base['user_id'] = $expense->user_id;
        }

        return $base;
    }

    protected function visibleOutletIds(Request $request): array
    {
        return array_map('intval', $request->user()->allOutletIds());
    }

    protected function authorizeExpense(Request $request, Expense $expense): void
    {
        abort_unless(in_array((int) $expense->outlet_id, $this->visibleOutletIds($request), true), 403);
    }

    protected function sanitizeOutletId(Request $request, int $outletId): int
    {
        abort_unless(in_array($outletId, $this->visibleOutletIds($request), true), 403);

        return $outletId;
    }

    protected function formOutlets(Request $request): array
    {
        return Outlet::query()
            ->whereIn('id', $this->visibleOutletIds($request))
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Outlet $outlet) => [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'slug' => $outlet->slug,
            ])
            ->values()
            ->all();
    }

    protected function categories(): array
    {
        return [
            'Bahan Cuci',
            'Listrik',
            'Air',
            'Gaji Karyawan',
            'Sewa',
            'Peralatan',
            'Transportasi',
            'Lainnya',
        ];
    }
}
