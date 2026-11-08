<x-admin-layout>
    <x-slot name="title">Pedidos</x-slot>

    @php
        $etiquetas = [
            'pendiente' => 'Pendiente',
            'en_preparacion' => 'En preparación',
            'listo' => 'Listo',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];

        $seleccionables = [
            'pendiente' => 'Pendiente',
            'en_preparacion' => 'En preparación',
            'entregado' => 'Entregado',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Gestión de pedidos</h2>
                <p class="mt-1 text-sm text-stone-600">
                    {{ $pedidos->total() }} {{ $pedidos->total() === 1 ? 'pedido' : 'pedidos' }} encontrados.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.pedidos.index') }}" class="flex items-end gap-2">
                <div>
                    <label for="estado" class="block text-sm font-medium text-stone-700">Filtrar por estado</label>
                    <select
                        id="estado"
                        name="estado"
                        class="mt-1.5 w-52 rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="">Todos</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->value }}" @selected($filtro === $estado->value)>
                                {{ $etiquetas[$estado->value] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-500/40"
                >
                    Filtrar
                </button>

                @if ($filtro !== '')
                    <a
                        href="{{ route('admin.pedidos.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-600 transition hover:border-amber-300 hover:text-amber-700"
                    >
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Orders table -->
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            @if ($pedidos->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M6 3h12a1 1 0 0 1 1 1v17l-3-2-2 2-2-2-2 2-2-2-3 2V4a1 1 0 0 1 1-1Z" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-stone-900">No hay pedidos para este filtro</h3>
                    <p class="mt-1 max-w-sm text-sm text-stone-500">
                        Cuando los clientes confirmen pedidos aparecerán aquí, listos para gestionar.
                    </p>
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
                                <th scope="col" class="px-6 py-3">Pago</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>

                        @foreach ($pedidos as $pedido)
                            <tbody x-data="{ abierto: false }" class="divide-y divide-stone-100">
                                <tr class="align-top transition hover:bg-stone-50/60">
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-stone-800">
                                        #{{ $pedido->id }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-stone-600">
                                        {{ $pedido->user?->name ?? 'Invitado' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-stone-500">
                                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-stone-800">
                                        S/ {{ number_format((float) $pedido->total, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-stone-600">
                                        {{ ucfirst($pedido->metodo_pago->value) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <x-estado-pedido :estado="$pedido->estado" />
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-end gap-2">
                                            <button
                                                type="button"
                                                @click="abierto = ! abierto"
                                                :aria-expanded="abierto.toString()"
                                                aria-controls="detalle-pedido-{{ $pedido->id }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                                            >
                                                <span x-text="abierto ? 'Ocultar detalle' : 'Ver detalle'">Ver detalle</span>
                                                <svg class="h-3.5 w-3.5 transition-transform" :class="abierto ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <form method="POST" action="{{ route('admin.pedidos.updateEstado', $pedido) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')

                                                <label for="estado-pedido-{{ $pedido->id }}" class="sr-only">
                                                    Cambiar estado del pedido #{{ $pedido->id }}
                                                </label>

                                                <select
                                                    id="estado-pedido-{{ $pedido->id }}"
                                                    name="estado"
                                                    class="w-40 rounded-lg border-stone-300 bg-white py-1.5 ps-3 pe-8 text-xs font-medium text-stone-800 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                                >
                                                    @if (! array_key_exists($pedido->estado->value, $seleccionables))
                                                        <option value="{{ $pedido->estado->value }}" selected disabled>
                                                            {{ $etiquetas[$pedido->estado->value] }}
                                                        </option>
                                                    @endif

                                                    @foreach ($seleccionables as $valor => $etiqueta)
                                                        <option value="{{ $valor }}" @selected($pedido->estado->value === $valor)>
                                                            {{ $etiqueta }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-stone-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-500/40"
                                                >
                                                    Guardar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <tr id="detalle-pedido-{{ $pedido->id }}" x-show="abierto" x-cloak class="bg-stone-50/60">
                                    <td colspan="7" class="px-6 py-5">
                                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                                            <div class="lg:col-span-2">
                                                <h4 class="text-xs font-semibold uppercase tracking-wider text-stone-500">
                                                    Detalle del pedido
                                                </h4>

                                                <ul class="mt-3 space-y-2">
                                                    @forelse ($pedido->detallesPedido as $linea)
                                                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-stone-200 bg-white px-4 py-3">
                                                            <span class="text-sm text-stone-700">
                                                                <span class="font-semibold text-stone-900">{{ $linea->cantidad }}×</span>
                                                                {{ $linea->producto?->nombre ?? 'Producto no disponible' }}
                                                            </span>

                                                            <span class="flex items-center gap-3 text-sm">
                                                                <span class="text-xs text-stone-500">
                                                                    S/ {{ number_format((float) $linea->precio_unitario, 2) }} c/u
                                                                </span>
                                                                <span class="font-semibold text-stone-900">
                                                                    S/ {{ number_format((float) $linea->subtotal, 2) }}
                                                                </span>
                                                            </span>
                                                        </li>
                                                    @empty
                                                        <li class="text-sm text-stone-500">Este pedido no tiene líneas registradas.</li>
                                                    @endforelse
                                                </ul>
                                            </div>

                                            <div class="space-y-3">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Notas</p>
                                                    <p class="mt-1 rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-600">
                                                        {{ $pedido->notas ?: 'Sin notas del cliente.' }}
                                                    </p>
                                                </div>

                                                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-800">Total del pedido</p>
                                                    <p class="mt-1 text-lg font-semibold text-amber-900">S/ {{ number_format((float) $pedido->total, 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>

                @if ($pedidos->hasPages())
                    <div class="border-t border-stone-200 px-6 py-4">
                        {{ $pedidos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-admin-layout>
