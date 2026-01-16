<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('services.index') }}" class="text-sm text-gray-500 hover:text-primary-600 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Services
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">✏️ Edit Service</h1>
    </div>

    <!-- Form Card -->
    <div class="card max-w-2xl">
        <form wire:submit="update" class="space-y-4 sm:space-y-6">
            @if($availableOutlets->count() > 1)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Outlet *</label>
                <select wire:model="outlet_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @foreach($availableOutlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </select>
                @error('outlet_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            @elseif($availableOutlets->count() == 1)
            <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Outlet:</span> {{ $availableOutlets->first()->name }}
                </p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Service *</label>
                <input type="text" wire:model="name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select wire:model="unit" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="pasang">Per Pasang</option>
                        <option value="pcs">Per Pcs</option>
                        <option value="kg">Per Kg</option>
                        <option value="meter">Per Meter</option>
                    </select>
                    @error('unit') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                    <input type="number" wire:model="price" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4">
                <a href="{{ route('services.index') }}" class="btn-secondary flex-1 text-center">Cancel</a>
                <button type="submit" class="btn-primary flex-1">Update Service</button>
            </div>
        </form>
    </div>
</div>
