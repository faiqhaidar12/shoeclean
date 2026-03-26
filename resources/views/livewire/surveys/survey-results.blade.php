<div class="py-8">
    <!-- Page Header -->
    <div class="mb-12">
        <a href="{{ $backRoute }}" class="inline-flex items-center gap-2 text-sm font-bold opacity-40 hover:opacity-100 transition-opacity mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="headline-editorial text-4xl lg:text-5xl" style="color: var(--sa-primary, inherit);">{{ $survey->title }}</h1>
        <p class="font-medium mt-2 opacity-50">
            {{ $totalResponses }} respons terkumpul
            @if($survey->outlet) — {{ $survey->outlet->name }} @endif
        </p>
    </div>

    @if($totalResponses === 0)
        <div class="bg-white rounded-[2rem] p-12 shadow-sm text-center">
            <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 opacity-20 bg-gray-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="font-manrope font-bold text-lg opacity-40">Belum ada respons</p>
            <p class="text-sm opacity-30 mt-1 mb-6">Bagikan link survey untuk mulai mengumpulkan feedback</p>
            <button onclick="navigator.clipboard.writeText('{{ url('/survey/' . $survey->slug) }}'); this.textContent='Tersalin!'; setTimeout(() => this.textContent='Salin Link Survey', 2000)"
                class="px-6 py-3 rounded-2xl text-white font-bold text-sm shadow-lg active:scale-95 transition-all" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
                Salin Link Survey
            </button>
        </div>
    @else
        <!-- Share Link -->
        <div class="rounded-[2rem] p-6 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="background-color: {{ $context === 'superadmin' ? 'var(--sa-surface-low, #e8e7f5)' : '#f0f5f3' }};">
            <div class="flex items-center gap-3 overflow-hidden min-w-0">
                <svg class="w-5 h-5 shrink-0 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <code class="text-xs font-bold truncate opacity-60">{{ url('/survey/' . $survey->slug) }}</code>
            </div>
            <button onclick="navigator.clipboard.writeText('{{ url('/survey/' . $survey->slug) }}'); this.querySelector('span').textContent='Tersalin!'; setTimeout(() => this.querySelector('span').textContent='Salin', 2000)"
                class="px-4 py-2 bg-white rounded-xl text-xs font-bold shadow-sm hover:shadow-md transition-all active:scale-95 shrink-0">
                <span>Salin</span>
            </button>
        </div>

        <!-- Question Stats -->
        <div class="space-y-6">
            @foreach($questionStats as $qIndex => $stat)
                <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center text-xs font-black text-white shadow-lg" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #4f46e5' : '#3a6758, #2d5245' }});">
                            {{ $qIndex + 1 }}
                        </div>
                        <div>
                            <h3 class="font-manrope font-bold">{{ $stat['question'] }}</h3>
                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full {{ $stat['type'] === 'rating' ? 'bg-amber-100 text-amber-600' : ($stat['type'] === 'text' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600') }}">
                                {{ $stat['type'] === 'rating' ? 'Rating' : ($stat['type'] === 'text' ? 'Teks' : 'Pilihan') }}
                            </span>
                        </div>
                    </div>

                    @if($stat['type'] === 'rating')
                        <div class="flex items-center gap-6 mb-6">
                            <div class="text-center">
                                <p class="text-5xl font-manrope font-extrabold" style="color: {{ $context === 'superadmin' ? 'var(--sa-secondary, #6366f1)' : '#3a6758' }};">{{ $stat['average'] }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1">Rata-rata</p>
                                <div class="flex gap-0.5 mt-2 justify-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($stat['average']) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="flex-1 space-y-1.5">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black w-4 text-right opacity-40">{{ $i }}</span>
                                        <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                            @php $pct = $stat['count'] > 0 ? ($stat['distribution'][$i] / $stat['count']) * 100 : 0; @endphp
                                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%; background: linear-gradient(90deg, {{ $context === 'superadmin' ? '#6366f1, #818cf8' : '#3a6758, #4a8068' }});"></div>
                                        </div>
                                        <span class="text-[10px] font-black w-6 opacity-40">{{ $stat['distribution'][$i] }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @elseif($stat['type'] === 'choice')
                        <div class="space-y-2">
                            @foreach($stat['distribution'] as $option => $count)
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold min-w-[120px] truncate">{{ $option }}</span>
                                    <div class="flex-1 h-6 bg-gray-100 rounded-full overflow-hidden">
                                        @php $pct = $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0; @endphp
                                        <div class="h-full rounded-full flex items-center px-3 transition-all duration-500" style="width: {{ max($pct, 5) }}%; background: linear-gradient(90deg, {{ $context === 'superadmin' ? '#8b5cf6, #a78bfa' : '#2d7a5e, #3a9d76' }});">
                                            <span class="text-[10px] font-black text-white">{{ $count }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black opacity-40 w-10 text-right">{{ round($pct) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    @elseif($stat['type'] === 'text')
                        @if(count($stat['answers']) > 0)
                            <div class="space-y-2 max-h-[200px] overflow-y-auto pr-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-200 [&::-webkit-scrollbar-thumb]:rounded-full">
                                @foreach($stat['answers'] as $answer)
                                    <div class="p-3 bg-gray-50 rounded-xl text-sm">
                                        "{{ $answer }}"
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm opacity-30 italic">Belum ada jawaban teks</p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Individual Responses -->
        <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm mt-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#ec4899, #f472b6' : '#d97706, #f59e0b' }});">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold">Responden</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">{{ $totalResponses }} orang</p>
                </div>
            </div>

            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-200 [&::-webkit-scrollbar-thumb]:rounded-full">
                @foreach($responses as $response)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black text-white shadow-sm" style="background: linear-gradient(135deg, {{ $context === 'superadmin' ? '#6366f1, #818cf8' : '#3a6758, #4a8068' }});">
                                {{ substr($response->respondent_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-manrope font-bold text-sm">{{ $response->respondent_name }}</p>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-30">{{ $response->respondent_type }} · {{ $response->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($response->respondent_phone)
                            <span class="text-xs font-bold opacity-30">{{ $response->respondent_phone }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
