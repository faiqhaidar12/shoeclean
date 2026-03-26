<div class="py-8">
    <!-- Page Header -->
    <div class="mb-12">
        <a href="{{ $context === 'superadmin' ? route('superadmin.surveys.index') : route('surveys.index') }}" class="inline-flex items-center gap-2 text-sm font-bold opacity-40 hover:opacity-100 transition-opacity mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl" style="color: var(--sa-primary, inherit);">Buat Survey Baru</h1>
        <p class="font-medium mt-2 opacity-50">
            {{ $context === 'superadmin' ? 'Survey ini akan ditampilkan untuk semua pengguna platform' : 'Survey ini akan diisi oleh pelanggan outlet Anda' }}
        </p>
    </div>

    <form wire:submit="save">
        <!-- Basic Info -->
        <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm mb-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #818cf8' : '#3a6758, #4a8068' }});">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold">Informasi Survey</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Detail Dasar</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest opacity-40 mb-2">Judul Survey *</label>
                    <input type="text" wire:model="title" placeholder="Contoh: Kepuasan Pelanggan 2026" class="w-full px-5 py-3 bg-gray-50 rounded-2xl border-0 font-bold text-sm focus:ring-2 focus:outline-none" style="--tw-ring-color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }};">
                    @error('title') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest opacity-40 mb-2">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" rows="3" placeholder="Bantu kami meningkatkan layanan dengan mengisi survey ini..." class="w-full px-5 py-3 bg-gray-50 rounded-2xl border-0 font-bold text-sm focus:ring-2 focus:outline-none resize-none" style="--tw-ring-color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }};"></textarea>
                </div>

                @if($context === 'owner' && count($outlets) > 1)
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest opacity-40 mb-2">Outlet *</label>
                        <select wire:model="outlet_id" class="w-full px-5 py-3 bg-gray-50 rounded-2xl border-0 font-bold text-sm focus:ring-2 focus:outline-none" style="--tw-ring-color: #3a6758;">
                            <option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet['id'] }}">{{ $outlet['name'] }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>
        </div>

        <!-- Questions -->
        <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm mb-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#8b5cf6, #a78bfa' : '#2d7a5e, #3a9d76' }});">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-manrope font-bold">Pertanyaan</h3>
                        <p class="text-xs font-black uppercase tracking-widest opacity-40">{{ count($questions) }} pertanyaan</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($questions as $qIndex => $question)
                    <div class="rounded-2xl p-6 relative" style="background-color: {{ $context === 'superadmin' ? 'var(--sa-surface-low, #e8e7f5)' : '#f0f5f3' }};">
                        <!-- Question number badge -->
                        <div class="absolute -top-3 -left-2 w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black text-white shadow-lg" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
                            {{ $qIndex + 1 }}
                        </div>

                        @if(count($questions) > 1)
                            <button type="button" wire:click="removeQuestion({{ $qIndex }})" class="absolute top-4 right-4 p-1.5 rounded-lg text-red-400 hover:bg-red-100 hover:text-red-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif

                        <div class="space-y-4 mt-2">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Pertanyaan *</label>
                                <input type="text" wire:model="questions.{{ $qIndex }}.question" placeholder="Contoh: Seberapa puas Anda dengan layanan kami?" class="w-full px-4 py-3 bg-white rounded-xl border-0 font-bold text-sm focus:ring-2 focus:outline-none" style="--tw-ring-color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }};">
                                @error("questions.{$qIndex}.question") <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Tipe Jawaban</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['rating' => '⭐ Rating', 'text' => '📝 Teks Bebas', 'choice' => '📋 Pilihan Ganda'] as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model.live="questions.{{ $qIndex }}.type" value="{{ $value }}" class="hidden peer">
                                            <span class="px-4 py-2 rounded-xl text-xs font-bold transition-all peer-checked:text-white peer-checked:shadow-lg bg-white opacity-60 peer-checked:opacity-100" style="peer-checked:background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <!-- Visual type indicator -->
                                <div class="mt-3 flex gap-2">
                                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $questions[$qIndex]['type'] === 'rating' ? 'bg-amber-100 text-amber-600' : ($questions[$qIndex]['type'] === 'text' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600') }}">
                                        {{ $questions[$qIndex]['type'] === 'rating' ? '⭐ Rating 1-5' : ($questions[$qIndex]['type'] === 'text' ? '📝 Teks Bebas' : '📋 Pilihan Ganda') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Choice Options -->
                            @if($questions[$qIndex]['type'] === 'choice')
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest opacity-40 mb-2">Opsi Pilihan</label>
                                    <div class="space-y-2">
                                        @foreach($questions[$qIndex]['options'] as $oIndex => $option)
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-full bg-white flex items-center justify-center text-[10px] font-black opacity-40">{{ chr(65 + $oIndex) }}</span>
                                                <input type="text" wire:model="questions.{{ $qIndex }}.options.{{ $oIndex }}" placeholder="Opsi {{ chr(65 + $oIndex) }}" class="flex-1 px-4 py-2.5 bg-white rounded-xl border-0 font-bold text-sm focus:ring-2 focus:outline-none" style="--tw-ring-color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }};">
                                                @if(count($questions[$qIndex]['options']) > 2)
                                                    <button type="button" wire:click="removeOption({{ $qIndex }}, {{ $oIndex }})" class="p-1.5 rounded-lg text-red-400 hover:bg-red-100 transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" wire:click="addOption({{ $qIndex }})" class="mt-2 px-4 py-2 bg-white rounded-xl text-xs font-bold opacity-50 hover:opacity-100 transition-all flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Tambah Opsi
                                    </button>
                                    @error("questions.{$qIndex}.options") <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" wire:click="addQuestion" class="mt-6 w-full py-4 border-2 border-dashed rounded-2xl font-bold text-sm opacity-40 hover:opacity-100 transition-all flex items-center justify-center gap-2" style="border-color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }}; color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }};">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Pertanyaan
            </button>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ $context === 'superadmin' ? route('superadmin.surveys.index') : route('surveys.index') }}" class="px-8 py-4 bg-white rounded-2xl font-bold text-sm shadow-sm hover:shadow-lg transition-all active:scale-95">
                Batal
            </a>
            <button type="submit" class="px-8 py-4 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center gap-2 text-sm" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Publish Survey
            </button>
        </div>
    </form>
</div>
