<div class="py-8">
    <!-- Page Header -->
    <div class="mb-12">
        <h1 class="headline-editorial text-4xl lg:text-5xl" style="color: var(--sa-primary, inherit);">
            {{ $context === 'superadmin' ? 'Survey Platform' : 'Survey Outlet' }}
        </h1>
        <p class="font-medium mt-2 opacity-50">
            {{ $context === 'superadmin' ? 'Kelola survey untuk seluruh pengguna platform' : 'Kelola survey untuk pelanggan outlet Anda' }}
        </p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 text-emerald-700 rounded-2xl font-bold text-sm flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #818cf8' : '#3a6758, #4a8068' }});">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div>
                <h3 class="font-manrope font-bold">Daftar Survey</h3>
                <p class="text-xs font-black uppercase tracking-widest opacity-40">{{ $surveys->count() }} survey</p>
            </div>
        </div>
        <a href="{{ $createRoute }}" class="px-6 py-3 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 active:scale-95 flex items-center gap-2 text-sm w-full sm:w-auto justify-center" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Buat Survey Baru
        </a>
    </div>

    <!-- Survey List -->
    @if($surveys->isEmpty())
        <div class="bg-white rounded-[2rem] p-12 shadow-sm text-center">
            <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 opacity-20" style="background-color: {{ $context === 'superadmin' ? 'var(--sa-surface-low, #e8e7f5)' : '#f0f5f3' }};">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="font-manrope font-bold text-lg opacity-40">Belum ada survey</p>
            <p class="text-sm opacity-30 mt-1">Buat survey pertama Anda untuk mulai mengumpulkan feedback</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($surveys as $survey)
                <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center {{ $survey->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-manrope font-bold text-lg truncate">{{ $survey->title }}</h3>
                                @if($survey->description)
                                    <p class="text-sm opacity-50 mt-1 line-clamp-2">{{ $survey->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $survey->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $survey->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-30">
                                        {{ $survey->responses_count }} Respons
                                    </span>
                                    @if($survey->outlet)
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-500">
                                            {{ $survey->outlet->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Copy Link -->
                            <button x-data="{ copied: false }" @click="
                                const url = '{{ url('/survey/' . $survey->slug) }}';
                                const ta = document.createElement('textarea');
                                ta.value = url;
                                ta.style.position = 'fixed';
                                ta.style.opacity = '0';
                                document.body.appendChild(ta);
                                ta.select();
                                document.execCommand('copy');
                                document.body.removeChild(ta);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                                class="px-4 py-2.5 bg-gray-100 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span x-text="copied ? 'Tersalin!' : 'Salin Link'"></span>
                            </button>

                            <!-- Results -->
                            <a href="{{ route($resultsRouteName, $survey) }}"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-white transition-all active:scale-95 flex items-center gap-2"
                                style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Hasil
                            </a>

                            <!-- Toggle -->
                            <button wire:click="toggleActive({{ $survey->id }})"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all active:scale-95 {{ $survey->is_active ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' }}">
                                {{ $survey->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>

                            <!-- Delete -->
                            <button wire:click="deleteSurvey({{ $survey->id }})" wire:confirm="Yakin ingin menghapus survey ini? Semua respons akan terhapus."
                                class="p-2.5 rounded-xl text-red-400 hover:bg-red-50 hover:text-red-600 transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
