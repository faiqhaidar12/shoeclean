<div class="py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8 mb-16">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-artisan-secondary mb-4">Manajemen Personalia</p>
            <h1 class="headline-editorial text-4xl lg:text-5xl italic">Registri Artisan</h1>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-[0.2em] mt-4">Mengawasi pengrajin di balik keunggulan</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-artisan-primary">
            Rekrut Artisan Baru
        </a>
    </div>

    <!-- Searching Protocol -->
    <div class="mb-12 relative group">
        <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none text-artisan-primary/20 group-focus-within:text-artisan-secondary transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari berdasarkan nama atau identitas profesional..." 
            class="artisan-input !pl-20 !py-6 !bg-artisan-surface-low/50 hover:!bg-artisan-surface-low transition-all">
    </div>

    @if($users->isEmpty())
        <div class="card-artisan p-20 text-center">
            <div class="w-24 h-24 bg-artisan-surface-low rounded-[2.5rem] flex items-center justify-center text-artisan-secondary/20 mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="headline-editorial text-2xl italic mb-4">Belum Ada Artisan Terdaftar</h3>
            <p class="text-[10px] text-artisan-primary/40 font-black uppercase tracking-widest">Mulai membangun tim profesional Anda</p>
        </div>
    @else
        <!-- Premium Staff Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($users as $user)
                <div class="card-artisan p-10 group hover:shadow-artisan-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-artisan-primary/5 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-artisan-primary/10 transition-colors"></div>
                    
                    <div class="flex items-start justify-between relative z-10">
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 bg-artisan-primary rounded-2xl flex items-center justify-center text-white font-manrope font-black text-xl shadow-artisan group-hover:scale-110 transition-transform">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-manrope font-black text-artisan-primary italic group-hover:text-artisan-secondary transition-colors">{{ $user->name }}</h3>
                                <p class="text-[9px] font-black uppercase tracking-widest text-artisan-primary/40 mt-1">{{ $user->email }}</p>
                                
                                <div class="mt-6 flex flex-wrap gap-2">
                                    @php $role = $user->roles->first()?->slug; @endphp
                                    @if($role)
                                        <span class="text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-full {{ match($role) {
                                            'owner' => 'bg-purple-50 text-purple-700',
                                            'admin' => 'bg-emerald-50 text-emerald-700',
                                            default => 'bg-artisan-surface-low text-artisan-primary/60'
                                        } }} shadow-sm">
                                            {{ match($role) {
                                                'owner' => 'Pemilik Bisnis',
                                                'admin' => 'Administrator Outlet',
                                                'staff' => 'Staf Karyawan',
                                                default => ucfirst($role)
                                            } }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('users.edit', $user->id) }}" class="w-10 h-10 bg-artisan-surface-low rounded-xl flex items-center justify-center text-artisan-primary/30 hover:bg-artisan-primary hover:text-white transition-all duration-300 shadow-sm active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                    
                    @if($user->outlet)
                        <div class="mt-12 pt-8 flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-artisan-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">{{ $user->outlet->name }}</span>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-16 sm:px-4">{{ $users->links() }}</div>
    @endif
</div>
