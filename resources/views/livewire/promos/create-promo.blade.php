<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('promos.index') }}" class="text-sm text-gray-500 hover:text-primary-600 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Promos
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">➕ Add Promo</h1>
    </div>

    <!-- Form Card -->
    <div class="card max-w-2xl">
        <form wire:submit="save" class="space-y-4 sm:space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo *</label>
                <input type="text" wire:model="code" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 uppercase font-mono" placeholder="DISKON20">
                @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo *</label>
                <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Diskon Hari Raya">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            @if($outlets->count() > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku di Outlet</label>
                <select wire:model="outlet_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk berlaku di semua outlet</p>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                    <select wire:model="type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed (Rp)</option>
                    </select>
                    @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai *</label>
                    <input type="number" wire:model="value" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="20">
                    @error('value') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Order (Rp)</label>
                    <input type="number" wire:model="min_order" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max. Discount (Rp)</label>
                    <input type="number" wire:model="max_discount" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Kosongkan jika tidak ada limit">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max. Usage</label>
                    <input type="number" wire:model="max_uses" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Kosongkan jika unlimited">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" wire:model="start_date" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir *</label>
                <input type="date" wire:model="end_date" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" wire:model="is_active" id="is_active" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <label for="is_active" class="text-sm text-gray-700">Aktifkan promo ini</label>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4">
                <a href="{{ route('promos.index') }}" class="btn-secondary flex-1 text-center">Cancel</a>
                <button type="submit" class="btn-primary flex-1">Save Promo</button>
            </div>
        </form>
    </div>
</div>
