<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav
    x-data="{
        open: false,
        lockedFeature: null,
        openLockedFeature(title, description) {
            this.lockedFeature = { title, description };
        },
        closeLockedFeature() {
            this.lockedFeature = null;
        }
    }"
    @keydown.escape.window="closeLockedFeature()"
    class="bg-white border-b border-gray-100"
>
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                <!-- Outlet Management Links -->
                @if(auth()->user()->isOwner())
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('outlets.index')" :active="request()->routeIs('outlets.*')" wire:navigate>
                            {{ __('Outlets') }}
                        </x-nav-link>
                    </div>
                @elseif(auth()->user()->isAdmin() && auth()->user()->outlet)
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('outlets.edit', auth()->user()->outlet->slug)" :active="request()->routeIs('outlets.*')" wire:navigate>
                            {{ __('Outlet') }}
                        </x-nav-link>
                    </div>
                @endif

                <!-- Owner + Admin Links -->
                @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')" wire:navigate>
                            {{ __('Services') }}
                        </x-nav-link>
                        @if(auth()->user()->hasFeature('team_management'))
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                                <span class="inline-flex items-center gap-2">
                                    <span>{{ __('Users') }}</span>
                                </span>
                            </x-nav-link>
                        @else
                            <button
                                type="button"
                                @click="openLockedFeature('Kelola Tim Terkunci', 'Fitur kelola admin dan staff tersedia mulai paket Pro. Upgrade paket untuk menambah dan mengatur tim outlet Anda.')"
                                class="inline-flex items-center gap-2 border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-gray-700 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                            >
                                <span>{{ __('Users') }}</span>
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-black uppercase tracking-[0.18em] text-amber-700">Pro</span>
                            </button>
                        @endif
                        <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')" wire:navigate>
                            {{ __('Expenses') }}
                        </x-nav-link>
                        @if(auth()->user()->hasFeature('promos'))
                            <x-nav-link :href="route('promos.index')" :active="request()->routeIs('promos.*')" wire:navigate>
                                <span class="inline-flex items-center gap-2">
                                    <span>{{ __('Promos') }}</span>
                                </span>
                            </x-nav-link>
                        @else
                            <button
                                type="button"
                                @click="openLockedFeature('Fitur Promo Terkunci', 'Anda sudah bisa melihat bahwa fitur promo tersedia di sistem. Untuk membuat dan mengelola promo, silakan upgrade ke paket Pro atau Business.')"
                                class="inline-flex items-center gap-2 border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-gray-700 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                            >
                                <span>{{ __('Promos') }}</span>
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-black uppercase tracking-[0.18em] text-amber-700">Pro</span>
                            </button>
                        @endif
                    </div>
                @endif

                <!-- All Roles (Owner + Admin + Staff) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                        {{ __('Customers') }}
                    </x-nav-link>
                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" wire:navigate>
                        {{ __('Orders') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if(auth()->user()->isOwner())
                    <div class="mr-4">
                        <livewire:outlet-switcher />
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->isOwner())
                <x-responsive-nav-link :href="route('outlets.index')" :active="request()->routeIs('outlets.*')" wire:navigate>
                    {{ __('Outlets') }}
                </x-responsive-nav-link>
            @elseif(auth()->user()->isAdmin() && auth()->user()->outlet)
                <x-responsive-nav-link :href="route('outlets.edit', auth()->user()->outlet->slug)" :active="request()->routeIs('outlets.*')" wire:navigate>
                    {{ __('Outlet') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>

    <div
        x-cloak
        x-show="lockedFeature"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-artisan-primary/45 backdrop-blur-sm" @click="closeLockedFeature()"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-white p-6 shadow-2xl sm:p-8">
            <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 shadow-sm">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.24em] text-amber-600">Upgrade Diperlukan</p>
            <h3 class="mt-3 text-2xl font-manrope font-extrabold text-artisan-primary" x-text="lockedFeature?.title"></h3>
            <p class="mt-3 text-sm font-semibold leading-relaxed text-artisan-primary/60" x-text="lockedFeature?.description"></p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                @if(auth()->user()->isOwner())
                    <a href="{{ route('subscription') }}" class="inline-flex w-full items-center justify-center rounded-[1.4rem] bg-artisan-primary px-5 py-4 text-sm font-bold text-white transition hover:bg-artisan-secondary">
                        Lihat Paket Upgrade
                    </a>
                @endif
                <button type="button" @click="closeLockedFeature()" class="inline-flex w-full items-center justify-center rounded-[1.4rem] bg-gray-100 px-5 py-4 text-sm font-bold text-artisan-primary/60 transition hover:bg-gray-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</nav>
