<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Outlet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $business_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        DB::transaction(function () use ($validated) {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            // Auto-assign Owner role
            $ownerRole = Role::where('slug', 'owner')->first();
            $user->roles()->attach($ownerRole);

            // Auto-create first outlet with business name
            $baseSlug = \Illuminate\Support\Str::slug($validated['business_name']);
            $slug = $baseSlug ?: 'outlet';
            $counter = 2;
            while (Outlet::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            Outlet::create([
                'owner_id' => $user->id,
                'name' => $validated['business_name'],
                'slug' => $slug,
                'address' => '-',
                'phone' => '-',
                'status' => 'active',
            ]);

            event(new Registered($user));
            Auth::login($user);
        });

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-manrope font-black text-artisan-primary italic tracking-tight mb-2">Registrasi Mitra.</h2>
        <p class="text-sm font-medium text-artisan-secondary/70">Bergabung dengan ekosistem manajemen perawatan sepatu eksklusif.</p>
    </div>

    <form wire:submit="register" class="space-y-6">
        <!-- Name -->
        <div>
            <label for="name" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">Nama Pemilik</label>
            <input 
                wire:model="name" 
                id="name" 
                class="artisan-input !bg-artisan-surface-low/50" 
                type="text" 
                name="name" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="Ex. Faiq Haidar"
            />
            @error('name') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        <!-- Business Name -->
        <div>
            <label for="business_name" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">Identitas Outlet/Outlet Cuci</label>
            <input 
                wire:model="business_name" 
                id="business_name" 
                class="artisan-input !bg-artisan-surface-low/50" 
                type="text" 
                name="business_name" 
                required 
                placeholder="Ex. Shoeclean Outlet JKT" 
            />
            @error('business_name') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">Kredensial Akses (Email)</label>
            <input 
                wire:model="email" 
                id="email" 
                class="artisan-input !bg-artisan-surface-low/50" 
                type="email" 
                name="email" 
                required 
                autocomplete="username" 
                placeholder="outlet@shoeclean.com"
            />
            @error('email') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">Kata Sandi</label>
            <input 
                wire:model="password" 
                id="password" 
                class="artisan-input !bg-artisan-surface-low/50"
                type="password"
                name="password"
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
            />
            @error('password') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-[0.2em] text-artisan-primary/40 mb-3">Validasi Kata Sandi</label>
            <input 
                wire:model="password_confirmation" 
                id="password_confirmation" 
                class="artisan-input !bg-artisan-surface-low/50"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
            />
            @error('password_confirmation') <p class="mt-2 text-[10px] text-red-600 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
        </div>

        <div class="pt-6">
            <button 
                type="submit" 
                class="btn-artisan-primary w-full py-4 !rounded-2xl"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-wait"
            >
                <span wire:loading.remove wire:target="register">
                    INISIALISASI OUTLET
                </span>
                <span wire:loading wire:target="register">
                    MEMPROSES...
                </span>
            </button>
        </div>

        <div class="text-center mt-8">
            <p class="text-xs font-medium text-artisan-primary/60">
                Sudah menjadi mitra artisan? 
                <a href="{{ route('login') }}" wire:navigate class="text-artisan-secondary font-black hover:underline uppercase tracking-widest ml-1">Portal Masuk</a>
            </p>
        </div>
    </form>
</div>
