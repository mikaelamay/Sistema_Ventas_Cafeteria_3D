@php
    $carritoSession = session('carrito', []);
    $carritoCantidad = is_array($carritoSession)
        ? max(0, (int) array_sum(array_map('intval', $carritoSession)))
        : 0;
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-stone-200 bg-stone-50/80 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex items-center gap-8">
                <!-- Brand -->
                <a href="{{ route('productos.index') }}" class="group flex shrink-0 items-center gap-2.5">
                    <x-application-logo class="block h-8 w-auto fill-current text-amber-600 transition-colors group-hover:text-amber-700" />
                    <span class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-800">
                        Cafetería
                    </span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-6 sm:flex">
                    <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">
                        {{ __('Catálogo') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Panel') }}
                        </x-nav-link>

                        @if (auth()->user()?->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                {{ __('Panel de Admin') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Session / Access -->
            <div class="hidden items-center gap-5 sm:flex">
                <a
                    href="{{ route('carrito.index') }}"
                    data-carrito-contador="{{ $carritoCantidad }}"
                    aria-label="{{ $carritoCantidad === 1 ? 'Ver carrito, 1 producto' : 'Ver carrito, '.$carritoCantidad.' productos' }}"
                    class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-stone-200 bg-white/70 text-stone-600 transition duration-150 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21 8H6" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    @if ($carritoCantidad > 0)
                        <span class="absolute -right-1.5 -top-1.5 inline-flex min-w-5 items-center justify-center rounded-full bg-amber-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-stone-50">
                            {{ $carritoCantidad > 99 ? '99+' : $carritoCantidad }}
                        </span>
                    @endif
                </a>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-white/70 px-3.5 py-1.5 text-sm font-medium text-stone-700 transition duration-150 ease-in-out hover:border-amber-300 hover:text-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/40">
                                <span>{{ auth()->user()->name }}</span>

                                <svg class="h-4 w-4 fill-current text-stone-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Mi perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="text-sm font-medium text-stone-600 transition-colors hover:text-amber-700">
                            {{ __('Iniciar sesión') }}
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-full bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-stone-50">
                            {{ __('Crear cuenta') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" :aria-expanded="open.toString()" aria-label="{{ __('Abrir menú de navegación') }}" class="inline-flex items-center justify-center rounded-md p-2 text-stone-500 transition duration-150 ease-in-out hover:bg-stone-100 hover:text-stone-700 focus:bg-stone-100 focus:text-stone-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden border-t border-stone-200 bg-stone-50 sm:hidden">
        <div class="space-y-1 pt-2 pb-3">
            <x-responsive-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">
                {{ __('Catálogo') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('carrito.index')" :active="request()->routeIs('carrito.*')">
                <span class="flex items-center justify-between gap-3">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21 8H6" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Carrito') }}
                    </span>

                    @if ($carritoCantidad > 0)
                        <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-amber-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white">
                            {{ $carritoCantidad > 99 ? '99+' : $carritoCantidad }}
                        </span>
                    @endif
                </span>
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Panel') }}
                </x-responsive-nav-link>

                @if (auth()->user()?->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                        {{ __('Panel de Admin') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Session Options -->
        <div class="border-t border-stone-200 pt-4 pb-4">
            @auth
                <div class="px-4">
                    <div class="text-base font-medium text-stone-800">{{ auth()->user()->name }}</div>
                    <div class="text-sm text-stone-500">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Mi perfil') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Cerrar sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    @if (Route::has('login'))
                        <x-responsive-nav-link :href="route('login')">
                            {{ __('Iniciar sesión') }}
                        </x-responsive-nav-link>
                    @endif

                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">
                            {{ __('Crear cuenta') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
