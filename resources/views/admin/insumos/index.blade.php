<x-admin-layout>
    <x-slot name="title">Inventario</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Inventario de insumos</h2>
                <p class="mt-1 text-sm text-stone-600">
                    {{ $insumos->total() }} {{ $insumos->total() === 1 ? 'insumo' : 'insumos' }} registrados.
                </p>
            </div>

            <a
                href="{{ route('admin.insumos.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" />
                </svg>
                Nuevo insumo
            </a>
        </div>

        <form method="GET" action="{{ route('admin.insumos.index') }}" role="search" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="buscar" class="block text-sm font-medium text-stone-700">Buscar</label>
                    <input
                        id="buscar"
                        type="search"
                        name="buscar"
                        value="{{ $buscar }}"
                        placeholder="Nombre o unidad"
                        class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500"
                    >
                </div>

                <div class="sm:w-64">
                    <label for="bajo_stock" class="block text-sm font-medium text-stone-700">Filtro</label>
                    <select
                        id="bajo_stock"
                        name="bajo_stock"
                        class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="" @selected(!$bajoStock)>Todos</option>
                        <option value="1" @selected($bajoStock)>Solo bajo stock</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-500/40"
                    >
                        Filtrar
                    </button>

                    @if ($buscar !== '' || $bajoStock)
                        <a
                            href="{{ route('admin.insumos.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-semibold text-stone-600 transition hover:border-amber-300 hover:text-amber-700"
                        >
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            @if ($insumos->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-stone-900">No hay insumos que coincidan</h3>
                    <p class="mt-1 max-w-sm text-sm text-stone-500">
                        Prueba con otro término de búsqueda o registra un nuevo insumo.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50/70">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                <th scope="col" class="px-6 py-3">Insumo</th>
                                <th scope="col" class="px-6 py-3">Unidad</th>
                                <th scope="col" class="px-6 py-3 text-right">Stock actual</th>
                                <th scope="col" class="px-6 py-3 text-right">Stock mínimo</th>
                                <th scope="col" class="px-6 py-3 text-right">Costo unit.</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($insumos as $insumo)
                                <php
                                    $bajoStock = $insumo->stock_actual <= $insumo->stock_minimo;
                                </php>
                                <tr class="transition hover:bg-stone-50/60 {{ $bajoStock ? 'bg-amber-50/40' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-stone-900">{{ $insumo->nombre }}</p>
                                            <p class="mt-0.5 text-xs text-stone-500">{{ $insumo->movimientosInventario()->count() }} movimientos registrados</p>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-stone-600">
                                        {{ $insumo->unidad_medida }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right font-medium">
                                        {{ number_format((float) $insumo->stock_actual, 3) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-xs">
                                        {{ number_format((float) $insumo->stock_minimo, 3) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-stone-600">
                                        {{ $insumo->costo_unitario ? 'S/ '.number_format((float) $insumo->costo_unitario, 2) : '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset
                                            {{ $bajoStock ? 'bg-amber-100 text-amber-800 ring-amber-200' : 'bg-emerald-100 text-emerald-800 ring-emerald-200' }}"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                                            {{ $bajoStock ? 'Bajo stock' : 'OK' }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('admin.insumos.edit', $insumo) }}"
                                                class="inline-flex items-center rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                                            >
                                                Editar
                                            </a>

                                            <a
                                                href="{{ route('admin.insumos.movimientos.create', $insumo) }}"
                                                class="inline-flex items-center rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                                            >
                                                Movimiento
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.insumos.destroy', $insumo) }}"
                                                onsubmit="return confirm('¿Eliminar este insumo? Esta acción no se puede deshacer.');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/40"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($insumos->hasPages())
                    <div class="border-t border-stone-200 px-6 py-4">
                        {{ $insumos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-admin-layout>
