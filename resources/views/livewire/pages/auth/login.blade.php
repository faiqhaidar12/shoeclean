<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-manrope font-black text-artisan-primary italic tracking-tight mb-2">Akses Portal Artisan.</h2>
        <p class="text-sm font-medium text-artisan-secondary/70">Otorisasi bisnis preservasi sepatu Anda</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-artisan-primary/5 border border-artisan-outline/50 rounded-2xl text-artisan-primary text-xs font-bold uppercase tracking-widest text-center">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">KREDENSIAL AKSES (EMAIL)</label>
            <input 
                wire:model="form.email" 
                id="email" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="artisan@namaoutlet.com"
                class="artisan-input !bg-artisan-surface-low/50"
            >
            @error('form.email')
                <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <label for="password" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40">KATA SANDI</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="text-[10px] font-black uppercase tracking-widest text-artisan-secondary hover:text-artisan-primary transition-colors">
                        Lupa Sandi?
                    </a>
                @endif
            </div>
            <input 
                wire:model="form.password" 
                id="password" 
                type="password"
                name="password"
                required 
                autocomplete="current-password"
                placeholder="••••••••"
                class="artisan-input !bg-artisan-surface-low/50"
            >
            @error('form.password')
                <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-2">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <input 
                    wire:model="form.remember" 
                    id="remember" 
                    type="checkbox" 
                    class="w-5 h-5 rounded-lg border-artisan-outline text-artisan-secondary focus:ring-artisan-secondary/20 transition-colors bg-white accent-artisan-secondary"
                >
                <span class="ml-3 text-xs font-bold text-artisan-primary/70 group-hover:text-artisan-primary transition-colors">Ingat sesi saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button 
                type="submit" 
                class="btn-artisan-primary w-full py-4 !rounded-2xl flex items-center justify-center gap-3"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-wait"
            >
                <span wire:loading.remove wire:target="login">
                    OTENTIKASI
                </span>
                <span wire:loading wire:target="login" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    MEMPROSES...
                </span>
            </button>
        </div>
        
        <div class="text-center mt-8">
            <p class="text-xs font-medium text-artisan-primary/60">
                Belum memiliki akun artisan? 
                <a href="{{ route('register') }}" wire:navigate class="text-artisan-secondary font-black hover:underline uppercase tracking-widest ml-1">Pendaftaran Mitra</a>
            </p>
        </div>
    </form>
</div>
