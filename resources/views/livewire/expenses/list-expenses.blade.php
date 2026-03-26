<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8 mb-16">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">Pengawasan Finansial</p>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Buku Besar Pengeluaran</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mt-4">Melacak modal operasional kita</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn-artisan-primary">
            Catat Pengeluaran Baru
        </a>
    </div>

    <!-- Filtering & Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Filters -->
        <div class="lg:col-span-2 card-artisan p-8 flex flex-col sm:flex-row gap-8 items-end">
            <div class="flex-1 w-full">
                <label class="block text-[10px] font-black text-artisan-primary/40 uppercase tracking-widest mb-4">Periode Fiskal</label>
                <div class="flex gap-4">
                    <select wire:model.live="month" class="artisan-input !py-4">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="year" class="artisan-input !py-4 w-32">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Monthly Total -->
        <div class="bg-artisan-primary rounded-[2rem] p-8 text-white shadow-artisan-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-white/10 transition-colors"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-4 relative z-10">Total Arus Keluar Periode</p>
            <p class="text-3xl font-manrope font-black relative z-10">Rp {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</p>
            <div class="mt-6 flex items-center gap-2 relative z-10">
                <div class="w-1.5 h-1.5 rounded-full bg-red-400"></div>
                <span class="text-[8px] font-black uppercase tracking-widest text-white/30">Komitmen Tercatat</span>
            </div>
        </div>
    </div>

    @if($expenses->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2.5rem] flex items-center justify-center text-artisan-secondary/20 mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="headline-editorial text-2xl italic mb-4">Belum Ada Pengeluaran Tercatat</h3>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">Laporan keuangan saat ini bersih</p>
        </div>
    @else
        <!-- Mobile/Tablet Flow -->
        <div class="block lg:hidden space-y-6">
            @foreach($expenses as $expense)
                <div class="card-artisan p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[8px] font-black uppercase tracking-widest bg-artisan-surface-low text-artisan-primary/60 px-3 py-1.5 rounded-full shadow-sm">{{ match($expense->category) {
                                'supplies' => 'Perlengkapan Material',
                                'utilities' => 'Utilitas Outlet',
                                'salary' => 'Honorarium Staf',
                                'rent' => 'Sewa Outlet',
                                'maintenance' => 'Pemeliharaan Fasilitas',
                                'other' => 'Lain-lain',
                                default => $expense->category
                            } }}</span>
                            <p class="text-sm font-manrope font-black text-artisan-primary italic mt-6">{{ $expense->description ?? 'Tanpa keterangan' }}</p>
                            <p class="text-[9px] text-artisan-primary/30 font-black uppercase tracking-widest mt-2">{{ $expense->expense_date->format('d M Y') }}</p>
                        </div>
                        <p class="text-xl font-manrope font-black text-red-600 italic">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-8 pt-6 border-t border-artisan-outline/10 flex justify-end gap-3">
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/30 hover:bg-artisan-primary hover:text-white transition-all duration-300 active:scale-95 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <button wire:click="delete({{ $expense->id }})" wire:confirm="Batalkan catatan ini? Tindakan ini akan dicatat." class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/20 hover:bg-red-600 hover:text-white transition-all duration-300 active:scale-95 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Fluid Table -->
        <div class="hidden lg:block card-artisan p-0 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-artisan-surface-low/50">
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Tanggal</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Klasifikasi</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Keterangan</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Node Outlet</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Alokasi</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-artisan-outline/10">
                    @foreach($expenses as $expense)
                        <tr class="hover:bg-artisan-surface-low/30 transition-colors group">
                            <td class="px-10 py-8 text-xs font-manrope font-bold text-artisan-primary/60">{{ $expense->expense_date->format('d M Y') }}</td>
                            <td class="px-10 py-8">
                                <span class="text-[8px] font-black uppercase tracking-widest bg-artisan-surface-low text-artisan-primary/60 px-3 py-1.5 rounded-full shadow-sm">
                                    {{ match($expense->category) {
                                        'supplies' => 'Perlengkapan Material',
                                        'utilities' => 'Utilitas Outlet',
                                        'salary' => 'Honorarium Staf',
                                        'rent' => 'Sewa Outlet',
                                        'maintenance' => 'Pemeliharaan Fasilitas',
                                        'other' => 'Lain-lain',
                                        default => $expense->category
                                    } }}
                                </span>
                            </td>
                            <td class="px-10 py-8 text-sm font-manrope font-black text-artisan-primary italic">{{ $expense->description ?? '--' }}</td>
                            <td class="px-10 py-8 text-[10px] font-black uppercase tracking-widest text-artisan-primary/30">{{ $expense->outlet->name }}</td>
                            <td class="px-10 py-8 text-right font-manrope font-black text-red-600 italic">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-10 py-8 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('expenses.edit', $expense->id) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-artisan-primary/30 hover:bg-artisan-primary hover:text-white transition-all duration-300 shadow-sm active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button wire:click="delete({{ $expense->id }})" wire:confirm="Batalkan catatan ini?" class="w-8 h-8 rounded-lg flex items-center justify-center text-artisan-primary/20 hover:bg-red-600 hover:text-white transition-all duration-300 shadow-sm active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16 sm:px-4">{{ $expenses->links() }}</div>
    @endif
</div>
