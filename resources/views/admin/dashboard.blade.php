<x-admin-layout>
    <x-slot name="title">Panel de administración</x-slot>

    <div class="space-y-8">
        <!-- Welcome -->
        <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.22em] text-amber-700">
                        Resumen de la cafetería
                    </span>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900 sm:text-3xl">
                        Hola, {{ auth()->user()->name }}
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600">
                        Gestiona el catálogo, revisa los pedidos del día y mantén la operación al día desde un solo lugar.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('admin.productos.create') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" />
                        </svg>
                        Nuevo producto
                    </a>

                    <a
                        href="{{ route('admin.pedidos.index') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                    >
                        Ver pedidos
                    </a>
                </div>
            </div>
        </section>

        <!-- Metrics -->
        <section aria-label="Métricas del panel" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-stone-500">Productos activos</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M20.5 7.28v9.44a2 2 0 0 1-1 1.74l-6.5 3.75a2 2 0 0 1-2 0L4.5 18.46a2 2 0 0 1-1-1.74V7.28a2 2 0 0 1 1-1.74L11 1.79a2 2 0 0 1 2 0l6.5 3.75a2 2 0 0 1 1 1.74Z" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">{{ $metricas['productos_activos'] }}</p>
                <p class="mt-1 text-xs text-stone-500">Disponibles en el catálogo público</p>
            </article>

            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-stone-500">Pedidos pendientes</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">{{ $metricas['pedidos_pendientes'] }}</p>
                <p class="mt-1 text-xs text-stone-500">Esperando preparación</p>
            </article>

            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-stone-500">Pedidos de hoy</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-stone-100 text-stone-700 ring-1 ring-inset ring-stone-200">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M8 3v4M16 3v4M3 10h18" stroke-linecap="round" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">{{ $metricas['pedidos_hoy'] }}</p>
                <p class="mt-1 text-xs text-stone-500">Registrados durante el día</p>
            </article>

            <article class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-stone-500">Ventas de hoy</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M12 3v18M8 7h5.5a2.5 2.5 0 0 1 0 5H10a2.5 2.5 0 0 0 0 5H16" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">
                    <span class="text-base font-medium text-stone-500">S/</span> {{ number_format($metricas['ventas_hoy'], 2) }}
                </p>
                <p class="mt-1 text-xs text-stone-500">Pedidos no cancelados de hoy</p>
            </article>
        </section>

        <!-- Recent orders -->
        <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold tracking-tight text-stone-900">Últimos pedidos</h2>
                    <p class="mt-0.5 text-sm text-stone-500">Los movimientos más recientes de la cafetería.</p>
                </div>

                <a href="{{ route('admin.pedidos.index') }}" class="text-sm font-semibold text-amber-700 transition hover:text-amber-800">
                    Ver todos
                </a>
            </div>

            @if ($ultimosPedidos->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-sm text-stone-500">Todavía no hay pedidos registrados.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50/70">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                <th scope="col" class="px-6 py-3">Pedido</th>
                                <th scope="col" class="px-6 py-3">Cliente</th>
                                <th scope="col" class="px-6 py-3">Fecha</th>
                                <th scope="col" class="px-6 py-3">Total</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($ultimosPedidos as $pedido)
                                <tr class="transition hover:bg-stone-50/60">
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-stone-800">#{{ $pedido->id }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ $pedido->user?->name ?? 'Invitado' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-stone-500">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-stone-800">S/ {{ number_format((float) $pedido->total, 2) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <x-estado-pedido :estado="$pedido->estado" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-admin-layout>
