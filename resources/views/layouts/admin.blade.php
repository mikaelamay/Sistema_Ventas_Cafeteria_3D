<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' · ' : '' }}Cafetería · Admin</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="h-full bg-stone-50 font-sans text-stone-800 antialiased">
        <div x-data="{ sidebarAbierto: false }" class="min-h-screen lg:flex">
            <!-- Mobile overlay -->
            <div
                x-cloak
                x-show="sidebarAbierto"
                x-transition.opacity
                @click="sidebarAbierto = false"
                class="fixed inset-0 z-40 bg-stone-950/50 backdrop-blur-sm lg:hidden"
                aria-hidden="true"
            ></div>

            <!-- Sidebar -->
            <aside
                id="admin-sidebar"
                x-cloak
                :class="sidebarAbierto ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-stone-800 bg-stone-900 transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
                aria-label="Navegación de administración"
            >
                <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-stone-800 px-6">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/15 text-amber-400 ring-1 ring-inset ring-amber-500/30">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path d="M4 8h13v6a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" stroke-linejoin="round" />
                                <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17" stroke-linejoin="round" />
                                <path d="M8 4c0 .8.7 1.1.7 1.9M12 4c0 .8.7 1.1.7 1.9" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span class="text-sm font-semibold tracking-tight text-white">
                            Cafetería <span class="text-amber-400">· Admin</span>
                        </span>
                    </a>

                    <button
                        @click="sidebarAbierto = false"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-stone-400 transition hover:bg-stone-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 lg:hidden"
                        aria-label="Cerrar menú de administración"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6" aria-label="Secciones de administración">
                    <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-stone-500">Gestión</p>

                    <a
                        href="{{ route('admin.dashboard') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-amber-500/10 text-amber-300 ring-1 ring-inset ring-amber-500/30' => request()->routeIs('admin.dashboard'),
                            'text-stone-300 hover:bg-stone-800 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                        ])
                        @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M4 13h6V4H4v9Zm10 7h6v-9h-6v9ZM4 20h6v-3H4v3Zm10-13h6V4h-6v3Z" stroke-linejoin="round" />
                        </svg>
                        Dashboard
                    </a>

                    <a
                        href="{{ route('admin.productos.index') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-amber-500/10 text-amber-300 ring-1 ring-inset ring-amber-500/30' => request()->routeIs('admin.productos.*'),
                            'text-stone-300 hover:bg-stone-800 hover:text-white' => ! request()->routeIs('admin.productos.*'),
                        ])
                        @if (request()->routeIs('admin.productos.*')) aria-current="page" @endif
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M20.5 7.28v9.44a2 2 0 0 1-1 1.74l-6.5 3.75a2 2 0 0 1-2 0L4.5 18.46a2 2 0 0 1-1-1.74V7.28a2 2 0 0 1 1-1.74L11 1.79a2 2 0 0 1 2 0l6.5 3.75a2 2 0 0 1 1 1.74Z" stroke-linejoin="round" />
                            <path d="m3.8 6.6 8.2 4.73 8.2-4.73M12 21.13V11.33" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Productos
                    </a>

                    <a
                        href="{{ route('admin.pedidos.index') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-amber-500/10 text-amber-300 ring-1 ring-inset ring-amber-500/30' => request()->routeIs('admin.pedidos.*'),
                            'text-stone-300 hover:bg-stone-800 hover:text-white' => ! request()->routeIs('admin.pedidos.*'),
                        ])
                        @if (request()->routeIs('admin.pedidos.*')) aria-current="page" @endif
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M6 3h12a1 1 0 0 1 1 1v17l-3-2-2 2-2-2-2 2-2-2-3 2V4a1 1 0 0 1 1-1Z" stroke-linejoin="round" />
                            <path d="M8.5 8h7M8.5 12h7M8.5 16h4" stroke-linecap="round" />
                        </svg>
                        Pedidos
                    </a>
                    <a
                        href="{{ route('admin.insumos.index') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-amber-500/10 text-amber-300 ring-1 ring-inset ring-amber-500/30' => request()->routeIs('admin.insumos.*'),
                            'text-stone-300 hover:bg-stone-800 hover:text-white' => ! request()->routeIs('admin.insumos.*'),
                        ])
                        @if (request()->routeIs('admin.insumos.*')) aria-current="page" @endif
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M19 4v16l-7-3-7 3V4a1 1 0 0 1 1-1h6l5 2.5a1 1 0 0 1 0 1.5l-5 2.5a1 1 0 0 1 0 1.5l-5 2.5V20a1 1 0 0 1-1 1h-2V4a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1Z" stroke-linejoin="round" />
                        </svg>
                        Inventario
                    </a>

                    <a
                        href="{{ route('admin.reportes.ventasPeriodo') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-amber-500/10 text-amber-300 ring-1 ring-inset ring-amber-500/30' => request()->routeIs('admin.reportes.*'),
                            'text-stone-300 hover:bg-stone-800 hover:text-white' => ! request()->routeIs('admin.reportes.*'),
                        ])
                        @if (request()->routeIs('admin.reportes.*')) aria-current="page" @endif
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M13 10V3L4 14h7v7l9-11h-7Z" stroke-linejoin="round" />
                            <path d="M18 8a1 1 0 0 1 1-1h2.586a1 1 0 0 1 0 2H19a1 1 0 0 1-1-1ZM16 18a1 1 0 0 1-1-1h-2.586a1 1 0 0 1 0 2H17a1 1 0 0 1 1-1Z" stroke-linejoin="round" />
                        </svg>
                        Reportes
                    </a>
                </nav>

                <div class="shrink-0 border-t border-stone-800 p-4">
                    <a
                        href="{{ route('productos.index') }}"
                        class="mb-3 flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-stone-400 transition hover:bg-stone-800 hover:text-amber-300"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M14 3h7v7M21 3l-9 9M10 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Ver tienda
                    </a>

                    <div class="flex items-center gap-3 rounded-xl bg-stone-800/60 px-3 py-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-500/15 text-sm font-semibold text-amber-300 ring-1 ring-inset ring-amber-500/30">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-stone-400">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-stone-400 transition hover:bg-stone-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path d="M15 12H3m0 0 4-4m-4 4 4 4M10 4h9a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-9" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Content -->
            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-stone-200 bg-stone-50/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                    <button
                        @click="sidebarAbierto = true"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-stone-600 transition hover:border-amber-300 hover:text-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/40 lg:hidden"
                        aria-label="Abrir menú de administración"
                        aria-controls="admin-sidebar"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-amber-700">Administración</p>
                        <h1 class="truncate text-lg font-semibold tracking-tight text-stone-900">
                            {{ $title ?? 'Panel de administración' }}
                        </h1>
                    </div>

                    <a
                        href="{{ route('productos.index') }}"
                        class="hidden items-center gap-1.5 rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40 sm:inline-flex"
                    >
                        Ver tienda
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </header>

                <x-flash-banner />

                <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
