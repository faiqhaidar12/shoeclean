<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-manrope font-extrabold tracking-tight" style="color: var(--sa-primary);">Feedback Platform</h1>
            <p class="text-sm opacity-60 font-medium mt-1">Kumpulan saran, ide, dan keluhan dari seluruh outlet.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-white rounded-2xl p-1 shadow-sm flex items-center border border-indigo-100">
                <button wire:click="$set('category', '')" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $category === '' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-indigo-400 hover:bg-indigo-50' }}">
                    Semua
                </button>
                <button wire:click="$set('category', 'saran')" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $category === 'saran' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-blue-400 hover:bg-blue-50' }}">
                    📝 Saran
                </button>
                <button wire:click="$set('category', 'ide')" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $category === 'ide' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'text-amber-400 hover:bg-amber-50' }}">
                    💡 Ide
                </button>
                <button wire:click="$set('category', 'keluhan')" class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $category === 'keluhan' ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'text-red-400 hover:bg-red-50' }}">
                    😤 Keluhan
                </button>
            </div>
        </div>
    </div>

    <!-- Feedbacks List -->
    <div class="space-y-4">
        @forelse($feedbacks as $feedback)
            <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-indigo-50 hover:shadow-md transition-all group">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Metadata -->
                    <div class="md:w-64 shrink-0 space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg {{ $feedback->category_color }} text-white shadow-sm">
                                {{ $feedback->category_label }}
                            </span>
                            <span class="text-[10px] font-bold opacity-40 uppercase tracking-widest">
                                {{ $feedback->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm font-bold text-artisan-primary">{{ $feedback->user->name }}</p>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-40">{{ $feedback->outlet?->name ?? 'Platform' }}</p>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute -left-2 -top-2 w-8 h-8 text-indigo-50 opacity-50 quote-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C20.1216 16 21.017 16.8954 21.017 18V21C21.017 22.1046 20.1216 23 19.017 23H16.017C14.9124 23 14.017 22.1046 14.017 21ZM5.017 21L5.017 18C5.017 16.8954 5.9124 16 7.017 16H10.017C11.1216 16 12.017 16.8954 12.017 18V21C12.017 22.1046 11.1216 23 10.017 23H7.017C5.9124 23 5.017 22.1046 5.017 21ZM19.017 14H16.017C14.9124 14 14.017 13.1046 14.017 12V9C14.017 7.89543 14.9124 7 16.017 7H17.017C17.017 5.34315 15.6738 4 14.017 4V2C16.2261 2 18.017 3.79086 18.017 6V12C18.017 13.1046 18.9124 14 20.017 14H19.017ZM10.017 14H7.017C5.9124 14 5.017 13.1046 5.017 12V9C5.017 7.89543 5.9124 7 7.017 7H8.017C8.017 5.34315 6.67385 4 5.017 4V2C7.22614 2 9.017 3.79086 9.017 6V12C9.017 13.1046 9.9124 14 11.017 14H10.017Z"/></svg>
                            <p class="text-artisan-primary font-medium leading-relaxed relative z-10">
                                {{ $feedback->message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] p-12 text-center border-2 border-dashed border-indigo-100">
                <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </div>
                <h3 class="font-bold text-artisan-primary text-xl">Belum ada feedback</h3>
                <p class="text-sm opacity-60 mt-2">Semua feedback dari outlet akan muncul di sini.</p>
            </div>
        @endforelse

        <div class="mt-8">
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>
