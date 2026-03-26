<div class="py-8">
    <div class="mb-12">
        <h1 class="font-manrope font-extrabold text-4xl lg:text-5xl tracking-tight" style="color: var(--sa-primary);">Insight Langganan</h1>
        <p class="font-medium mt-2 opacity-50">Pantau distribusi plan owner, subscription aktif, dan akun yang perlu follow-up.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
        <div class="rounded-[2rem] p-8 shadow-sm bg-white"><p class="text-xs font-black uppercase tracking-[0.2em] opacity-40 mb-2">Owner Free</p><p class="text-4xl font-manrope font-extrabold" style="color: var(--sa-primary);">{{ $freeOwnersCount }}</p></div>
        <div class="rounded-[2rem] p-8 shadow-sm text-white" style="background: linear-gradient(135deg, #2563eb, #4f46e5);"><p class="text-xs font-black uppercase tracking-[0.2em] text-white/50 mb-2">Owner Pro</p><p class="text-4xl font-manrope font-extrabold">{{ $proOwnersCount }}</p></div>
        <div class="rounded-[2rem] p-8 shadow-sm text-white" style="background: linear-gradient(135deg, #7c3aed, #a855f7);"><p class="text-xs font-black uppercase tracking-[0.2em] text-white/50 mb-2">Owner Business</p><p class="text-4xl font-manrope font-extrabold">{{ $businessOwnersCount }}</p></div>
        <div class="rounded-[2rem] p-8 shadow-sm text-white" style="background: linear-gradient(135deg, #dc2626, #f97316);"><p class="text-xs font-black uppercase tracking-[0.2em] text-white/50 mb-2">Hampir Expired</p><p class="text-4xl font-manrope font-extrabold">{{ $expiringSubscriptions->count() }}</p></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_1.3fr] gap-6">
        <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #f97316, #fb923c);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-12 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Segera Berakhir</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Perlu follow-up dalam 7 hari</p>
                </div>
            </div>
            @if($expiringSubscriptions->isEmpty())
                <div class="rounded-[1.6rem] p-5 text-sm font-semibold opacity-50" style="background-color: var(--sa-surface-low);">Tidak ada subscription yang akan berakhir dalam 7 hari ke depan.</div>
            @else
                <div class="space-y-3">
                    @foreach($expiringSubscriptions as $subscription)
                        <div class="rounded-[1.6rem] p-4" style="background-color: var(--sa-surface-low);">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-manrope font-bold text-sm truncate" style="color: var(--sa-primary);">{{ $subscription->user->name ?? '-' }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1">{{ strtoupper($subscription->plan) }}</p>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-rose-500 whitespace-nowrap">{{ $subscription->daysRemaining() }} hari lagi</span>
                            </div>
                            <p class="text-xs mt-2 opacity-50">{{ $subscription->user->email ?? '-' }} • Berakhir {{ $subscription->expires_at?->format('d M Y') ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Riwayat Subscription</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Filter plan dan status</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full md:w-auto">
                    <select wire:model.live="planFilter" class="px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                        <option value="">Semua Plan</option>
                        <option value="pro">Pro</option>
                        <option value="business">Business</option>
                    </select>
                    <select wire:model.live="statusFilter" class="px-5 py-3 bg-slate-50 rounded-2xl border-0 shadow-sm font-semibold text-sm focus:ring-2 focus:outline-none" style="color: var(--sa-primary); --tw-ring-color: var(--sa-secondary);">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($subscriptions as $subscription)
                    <div class="rounded-[1.6rem] p-4" style="background-color: var(--sa-surface-low);">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-manrope font-bold text-sm truncate" style="color: var(--sa-primary);">{{ $subscription->user->name ?? '-' }}</p>
                                <p class="text-xs opacity-50 mt-1">{{ $subscription->user->email ?? '-' }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $subscription->plan === 'business' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ strtoupper($subscription->plan) }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs opacity-60">
                            <span>Status: {{ strtoupper($subscription->status) }}</span>
                            <span>Mulai: {{ $subscription->started_at?->format('d M Y') ?? '-' }}</span>
                            <span>Berakhir: {{ $subscription->expires_at?->format('d M Y') ?? 'Tanpa batas' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[1.6rem] p-5 text-sm font-semibold opacity-50" style="background-color: var(--sa-surface-low);">Belum ada subscription yang cocok dengan filter saat ini.</div>
                @endforelse
            </div>

            <div class="mt-6">{{ $subscriptions->links() }}</div>
        </div>
    </div>
</div>
