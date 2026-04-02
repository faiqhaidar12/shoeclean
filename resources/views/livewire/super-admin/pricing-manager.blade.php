<div class="py-8">
    <div class="mb-10">
        <h1 class="font-manrope font-extrabold text-4xl lg:text-5xl tracking-tight" style="color: var(--sa-primary);">Kelola Harga SaaS</h1>
        <p class="font-medium mt-2 opacity-50">Atur harga paket, status publish, dan tampilan coming soon untuk landing page serta dashboard owner.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-8 rounded-[1.6rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">{{ $editingId ? 'Ubah harga' : 'Tambah harga' }}</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Paket dan top-up</p>
                </div>
                @if($editingId)
                    <button wire:click="resetForm" type="button" class="px-4 py-2 rounded-2xl bg-slate-100 text-sm font-bold" style="color: var(--sa-primary);">
                        Batal
                    </button>
                @endif
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Key</label>
                        <input wire:model="planKey" type="text" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="pro">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Nama Paket</label>
                        <input wire:model="name" type="text" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="Pro">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Subjudul</label>
                        <input wire:model="subtitle" type="text" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="Untuk 1 outlet yang sudah aktif">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">CTA</label>
                        <input wire:model="cta" type="text" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="Upgrade ke Pro">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Harga</label>
                        <input wire:model="price" type="number" min="0" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="75000">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Batas Pesanan</label>
                        <input wire:model="order_limit" type="number" min="0" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="100">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Maks. Cabang</label>
                        <input wire:model="max_outlets" type="number" min="0" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="1">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Kuota Top-up</label>
                        <input wire:model="quota" type="number" min="0" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="500">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Deskripsi</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-[1.5rem] border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="Deskripsi paket untuk landing dan dashboard owner."></textarea>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Fitur (satu baris satu fitur)</label>
                    <textarea wire:model="featuresText" rows="6" class="w-full rounded-[1.5rem] border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);" placeholder="1 cabang&#10;Pesanan tanpa batas"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <label class="flex items-center justify-between rounded-[1.4rem] bg-slate-50 px-4 py-4 text-sm font-bold" style="color: var(--sa-primary);">
                        <span>Sudah dipublish</span>
                        <input wire:model="is_published" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </label>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[0.2em] opacity-40">Urutan</label>
                        <input wire:model="sort_order" type="number" min="0" class="w-full rounded-2xl border-0 bg-slate-50 px-4 py-3 text-sm font-semibold shadow-sm focus:outline-none focus:ring-2" style="--tw-ring-color: var(--sa-secondary); color: var(--sa-primary);">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-[1.4rem] px-5 py-4 text-sm font-black uppercase tracking-[0.2em] text-white shadow-lg" style="background: linear-gradient(135deg, #312e81, #4f46e5);">
                    {{ $editingId ? 'Simpan perubahan' : 'Simpan harga' }}
                </button>
            </form>
        </div>

        <div class="bg-white rounded-[2rem] p-5 md:p-8 shadow-sm">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="font-manrope font-bold" style="color: var(--sa-primary);">Daftar harga</h3>
                    <p class="text-xs font-black uppercase tracking-widest opacity-40">Landing dan dashboard owner</p>
                </div>
                <div class="rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em]" style="background-color: var(--sa-surface-low); color: var(--sa-secondary);">
                    {{ $plans->count() }} item
                </div>
            </div>

            <div class="space-y-3">
                @forelse($plans as $plan)
                    <div class="rounded-[1.6rem] p-4" style="background-color: var(--sa-surface-low);">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-manrope font-bold text-sm" style="color: var(--sa-primary);">{{ $plan->name }}</p>
                                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $plan->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $plan->is_published ? 'Publish' : 'Coming Soon' }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1">{{ strtoupper($plan->key) }} · urutan {{ $plan->sort_order }}</p>
                                <p class="text-sm mt-2 opacity-60">{{ $plan->subtitle }}</p>
                                <p class="text-sm mt-2 font-bold" style="color: var(--sa-primary);">Rp{{ number_format((int) $plan->price, 0, ',', '.') }}{{ $plan->key === 'topup' ? '' : '/bulan' }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button wire:click="edit({{ $plan->id }})" type="button" class="rounded-full bg-white px-4 py-2 text-xs font-bold shadow-sm" style="color: var(--sa-primary);">
                                    Ubah
                                </button>
                                <button wire:click="delete({{ $plan->id }})" wire:confirm="Yakin ingin menghapus harga ini?" type="button" class="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 shadow-sm">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[1.6rem] p-5 text-sm font-semibold opacity-50" style="background-color: var(--sa-surface-low);">Belum ada harga yang tersimpan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
