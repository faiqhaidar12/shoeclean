<div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-artisan-primary/40 backdrop-blur-md"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    
    {{-- Backdrop --}}
    <div class="absolute inset-0" @click="open = false"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white rounded-[2.5rem] p-8 md:p-10 max-w-lg w-full shadow-2xl overflow-hidden"
        x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-95 translate-y-8" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
        
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-secondary/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-amber-400/5 rounded-full -ml-16 -mb-16 blur-2xl"></div>

        <div class="relative">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-manrope font-extrabold text-2xl text-artisan-primary">Kritik & Saran</h3>
                        <p class="text-xs text-artisan-primary/40 font-black uppercase tracking-widest mt-1 italic">Tulis feedback Anda</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 bg-gray-50 text-artisan-primary/40 hover:text-artisan-primary rounded-xl transition-all active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if($sent)
                <div class="py-12 text-center" x-init="setTimeout(() => open = false, 3000)">
                    <div class="w-24 h-24 mx-auto bg-emerald-100 rounded-[2rem] flex items-center justify-center mb-6 text-emerald-600 shadow-xl shadow-emerald-500/10">
                        <svg class="w-12 h-12 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h4 class="font-manrope font-extrabold text-2xl text-artisan-primary mb-2">Terima Kasih!</h4>
                    <p class="text-sm font-bold text-artisan-primary/50">Feedback Anda sudah kami terima dan akan segera ditinjau. Senang bisa terus berinovasi bersama Anda!</p>
                    
                    <button @click="open = false" class="mt-10 px-8 py-3 bg-artisan-primary text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all">
                        Kembali ke Dashboard
                    </button>
                </div>
            @else
                <div class="space-y-8">
                    {{-- Category --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-4 px-1">Pilih Kategori</p>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="category" value="saran" class="hidden peer">
                                <div class="h-full flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-gray-100 text-center transition-all peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 peer-checked:shadow-lg hover:bg-gray-50">
                                    <span class="text-2xl mb-1">📝</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none">Saran</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="category" value="ide" class="hidden peer">
                                <div class="h-full flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-gray-100 text-center transition-all peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 peer-checked:shadow-lg hover:bg-gray-50">
                                    <span class="text-2xl mb-1">💡</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none">Ide</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="category" value="keluhan" class="hidden peer">
                                <div class="h-full flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-gray-100 text-center transition-all peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 peer-checked:shadow-lg hover:bg-gray-50">
                                    <span class="text-2xl mb-1">😤</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none">Keluhan</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/30 mb-4 px-1">Isi Pesan Anda</p>
                        <textarea 
                            wire:model="message" 
                            rows="5"
                            placeholder="Apa yang bisa kami tingkatkan atau ide menarik apa yang Anda miliki?"
                            class="w-full px-6 py-5 bg-gray-50 rounded-[1.5rem] border-2 border-gray-100 text-sm font-semibold text-artisan-primary focus:ring-4 focus:ring-artisan-secondary/10 focus:border-artisan-secondary focus:outline-none transition-all resize-none placeholder:text-artisan-primary/20"
                        ></textarea>
                        @error('message') <span class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-2 block px-2 italic">{{ $message }}</span> @enderror
                    </div>

                    {{-- Submit --}}
                    <button 
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-5 bg-gradient-to-r from-artisan-primary to-indigo-900 text-white font-manrope font-extrabold rounded-[1.5rem] shadow-xl hover:shadow-2xl transition-all active:scale-[0.98] flex items-center justify-center gap-3 group"
                    >
                        <span wire:loading.remove>Kirim Feedback</span>
                        <span wire:loading>Mengirim...</span>
                        <svg wire:loading.remove class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                    
                    <p class="text-center text-[9px] font-black uppercase tracking-widest text-artisan-primary/20">Feedback ini akan terkirim langsung ke Super Admin platform.</p>
                </div>
            @endif
        </div>
    </div>
</div>
