<div class="min-h-screen flex items-center justify-center py-8 px-4">
    <div class="w-full max-w-xl">
        {{-- Step 1: Intro & Identity --}}
        @if($step === 1)
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-xl mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h1 class="font-manrope font-extrabold text-3xl text-slate-900 mb-2">{{ $survey->title }}</h1>
                @if($survey->description)
                    <p class="text-slate-500 leading-relaxed">{{ $survey->description }}</p>
                @endif
                @if($survey->outlet)
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-500 mt-3">{{ $survey->outlet->name }}</p>
                @endif
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100">
                <h2 class="font-manrope font-bold text-lg text-slate-900 mb-6">Identitas Anda</h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Nama Lengkap *</label>
                        <input type="text" wire:model="respondent_name" placeholder="Masukkan nama Anda" class="w-full px-5 py-3.5 bg-slate-50 rounded-2xl border-0 font-semibold text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder:text-slate-300">
                        @error('respondent_name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">No. HP (Opsional)</label>
                        <input type="text" wire:model="respondent_phone" placeholder="08xxxxxxxxxx" class="w-full px-5 py-3.5 bg-slate-50 rounded-2xl border-0 font-semibold text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder:text-slate-300">
                    </div>

                    @if($survey->type === 'platform')
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Anda adalah</label>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="respondent_type" value="customer" class="hidden peer">
                                    <div class="p-4 bg-slate-50 rounded-2xl text-center font-bold text-sm transition-all peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:shadow-lg text-slate-400">
                                        🛍️ Pelanggan
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model="respondent_type" value="owner" class="hidden peer">
                                    <div class="p-4 bg-slate-50 rounded-2xl text-center font-bold text-sm transition-all peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:shadow-lg text-slate-400">
                                        🏪 Pemilik Outlet
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endif
                </div>

                <button wire:click="nextStep" class="w-full mt-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all active:scale-[0.98] text-sm">
                    Mulai Survey →
                </button>
            </div>
        @endif

        {{-- Step 2: Questions --}}
        @if($step === 2)
            <div class="text-center mb-8">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-2">{{ $survey->title }}</p>
                <h1 class="font-manrope font-extrabold text-2xl text-slate-900">Pertanyaan Survey</h1>
                <p class="text-slate-400 text-sm mt-1">{{ count($questions) }} pertanyaan</p>
            </div>

            <div class="space-y-6">
                @foreach($questions as $qIndex => $question)
                    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-xl border border-slate-100">
                        <div class="flex items-start gap-3 mb-5">
                            <span class="w-8 h-8 shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-black shadow-lg">{{ $qIndex + 1 }}</span>
                            <h3 class="font-bold text-slate-900 leading-relaxed">{{ $question['question'] }}</h3>
                        </div>

                        @if($question['type'] === 'rating')
                            <div class="flex justify-center gap-2 py-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="setRating({{ $question['id'] }}, {{ $i }})"
                                        class="w-12 h-12 rounded-2xl transition-all duration-200 flex items-center justify-center text-lg {{ ($answers[$question['id']] ?? 0) >= $i ? 'bg-amber-400 text-white shadow-lg scale-110' : 'bg-slate-100 text-slate-300 hover:bg-amber-100 hover:text-amber-400' }}">
                                        ★
                                    </button>
                                @endfor
                            </div>
                            <p class="text-center text-xs text-slate-400 font-bold">
                                @if(($answers[$question['id']] ?? 0) > 0)
                                    {{ ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'][$answers[$question['id']]] }}
                                @else
                                    Tap untuk memberi rating
                                @endif
                            </p>
                            @error('answers.' . $question['id']) <span class="text-red-500 text-xs font-bold mt-2 block text-center">{{ $message }}</span> @enderror

                        @elseif($question['type'] === 'text')
                            <textarea wire:model="answers.{{ $question['id'] }}" rows="3" placeholder="Tulis jawaban Anda di sini..." class="w-full px-5 py-3.5 bg-slate-50 rounded-2xl border-0 font-semibold text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none placeholder:text-slate-300"></textarea>

                        @elseif($question['type'] === 'choice')
                            <div class="space-y-2">
                                @foreach($question['options'] ?? [] as $option)
                                    @if(trim($option) !== '')
                                        <label class="block cursor-pointer">
                                            <input type="radio" wire:model="answers.{{ $question['id'] }}" value="{{ $option }}" class="hidden peer">
                                            <div class="p-4 bg-slate-50 rounded-2xl font-semibold text-sm transition-all peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:shadow-lg text-slate-600 hover:bg-slate-100 flex items-center gap-3">
                                                <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-white peer-checked:bg-white shrink-0 flex items-center justify-center">
                                                    @if(($answers[$question['id']] ?? '') === $option)
                                                        <div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                                                    @endif
                                                </div>
                                                {{ $option }}
                                            </div>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            @error('answers.' . $question['id']) <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3 mt-8">
                <button wire:click="prevStep" class="flex-1 py-4 bg-white text-slate-600 font-bold rounded-2xl shadow-sm hover:shadow-lg transition-all active:scale-[0.98] text-sm border border-slate-200">
                    ← Kembali
                </button>
                <button wire:click="submit" class="flex-1 py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all active:scale-[0.98] text-sm">
                    Kirim Survey ✓
                </button>
            </div>
        @endif

        {{-- Step 3: Thank You --}}
        @if($step === 3)
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white shadow-xl mb-8 animate-bounce">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="font-manrope font-extrabold text-3xl text-slate-900 mb-3">Terima Kasih! 🎉</h1>
                <p class="text-slate-500 leading-relaxed max-w-sm mx-auto">
                    Respons Anda telah berhasil dikirim. Feedback Anda sangat berarti dalam meningkatkan layanan kami.
                </p>

                <div class="mt-8 p-6 bg-white rounded-3xl shadow-xl border border-slate-100 inline-block">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Survey</p>
                    <p class="font-manrope font-bold text-slate-900">{{ $survey->title }}</p>
                </div>
            </div>
        @endif

        <!-- Branding -->
        <div class="text-center mt-12">
            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-slate-300">Powered by ShoeClean</p>
        </div>
    </div>
</div>
