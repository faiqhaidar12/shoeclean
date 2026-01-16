<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">💰 Expenses</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengeluaran outlet</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn-primary inline-flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Expense
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select wire:model.live="month" class="w-full sm:w-48 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select wire:model.live="year" class="w-full sm:w-32 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Monthly Total -->
    <div class="card bg-gradient-to-br from-red-500 to-orange-500 text-white mb-6">
        <p class="text-sm text-red-100">Total Expenses Bulan Ini</p>
        <p class="text-2xl sm:text-3xl font-bold mt-1">Rp {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</p>
    </div>

    @if($expenses->isEmpty())
        <div class="card text-center py-12">
            <div class="text-5xl mb-4">📊</div>
            <p class="text-gray-500 text-lg">Belum ada pengeluaran</p>
        </div>
    @else
        <!-- Mobile: Card View -->
        <div class="block lg:hidden space-y-3">
            @foreach($expenses as $expense)
                <div class="card">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="badge bg-gray-100 text-gray-600">{{ $expense->category }}</span>
                            <p class="font-medium text-gray-900 mt-2">{{ $expense->description ?? 'No description' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $expense->expense_date->format('d M Y') }}</p>
                        </div>
                        <p class="text-lg font-bold text-red-600">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                    </div>
                     <div class="mt-3 pt-3 border-t flex justify-end gap-2">
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="p-2 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <button wire:click="delete({{ $expense->id }})" wire:confirm="Yakin ingin menghapus pengeluaran ini?" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop: Table -->
        <div class="hidden lg:block card overflow-hidden p-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Outlet</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->expense_date->format('d M Y') }}</td>
                            <td class="px-6 py-4"><span class="badge bg-gray-100 text-gray-600">{{ $expense->category }}</span></td>
                            <td class="px-6 py-4 text-gray-900">{{ $expense->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $expense->outlet->name }}</td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                                <button wire:click="delete({{ $expense->id }})" wire:confirm="Yakin ingin menghapus ini?" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $expenses->links() }}</div>
    @endif
</div>
