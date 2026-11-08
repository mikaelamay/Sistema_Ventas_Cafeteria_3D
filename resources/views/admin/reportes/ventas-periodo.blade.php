<x-admin-layout>
    <x-slot name="title">Ventas por período</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Ventas por período</h2>
                <p class="mt-1 text-sm text-stone-600">
                    Evolución de ventas y totales en el rango seleccionado.
                </p>
            </div>

            <a
                href="{{ route('admin.reportes.ventasProducto') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:border-amber-300 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 0 1-1.414 0l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L5.414 9H17a1 1 0 1 1 0 2H5.414l4.293 4.293a1 1 0 0 1 0 1.414Z" clip-rule="evenodd" />
                </svg>
                Ranking por producto
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reportes.ventasPeriodo') }}" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1 sm:w-72">
                    <label for="fecha_inicio" class="block text-sm font-medium text-stone-700">Desde</label>
                    <input
                        id="fecha_inicio"
                        name="fecha_inicio"
                        type="date"
                        value="{{ $fechaInicio }}"
                        class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                </div>

                <div class="flex-1 sm:w-72">
                    <label for="fecha_fin" class="block text-sm font-medium text-stone-700">Hasta</label>
                    <input
                        id="fecha_fin"
                        name="fecha_fin"
                        type="date"
                        value="{{ $fechaFin }}"
                        class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2"
                >
                    Filtrar
                </button>
            </div>
        </form>

        <!-- Resumen -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-stone-500">Total vendido</p>
                <p class="mt-1 text-3xl font-semibold tracking-tight text-stone-900">
                    S/ {{ number_format((float) $totalVendido, 2) }}
                </p>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-stone-500">Número de pedidos</p>
                <p class="mt-1 text-3xl font-semibold tracking-tight text-stone-900">
                    {{ $numeroPedidos }}
                </p>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-stone-500">Ticket promedio</p>
                <p class="mt-1 text-3xl font-semibold tracking-tight text-stone-900">
                    S/ {{ number_format($numeroPedidos > 0 ? (float) $totalVendido / $numeroPedidos : 0, 2) }}
                </p>
            </div>
        </div>

        <!-- Evolución -->
        <section class="rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-200 bg-stone-50 px-6 py-4">
                <h3 class="text-base font-semibold tracking-tight text-stone-900">Evolución diaria</h3>
            </div>

            <div class="p-6">
                @if ($evolucion->isEmpty())
                    <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                        <p class="text-sm text-stone-500">No hay ventas en el rango seleccionado.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-100 text-sm">
                            <thead class="bg-stone-50/70">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                    <th scope="col" class="px-6 py-3">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-right">Pedidos</th>
                                    <th scope="col" class="px-6 py-3 text-right">Total</th>
                                    <th scope="col" class="px-6 py-3">Progreso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                <?php $maxTotal = $evolucion->max('total') ?: 1; ?>
                                @foreach ($evolucion as $dia)
                                    <tr>
                                        <td class="px-6 py-3 text-stone-700">{{ $dia['fecha'] }}</td>
                                        <td class="px-6 py-3 text-right font-medium text-stone-800">{{ $dia['pedidos'] }}</td>
                                        <td class="px-6 py-3 text-right font-medium text-stone-800">S/ {{ number_format($dia['total'], 2) }}</td>
                                        <td class="px-6 py-3">
                                            <div class="w-full h-2 rounded-full bg-stone-100 overflow-hidden">
                                                <div
                                                    class="h-full bg-amber-500"
                                                    style="width: {{ (float) $dia['total'] / $maxTotal * 100 }}%"
                                                    aria-valuenow="{{ $dia['total'] }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="{{ $maxTotal }}"
                                                    role="progressbar"
                                                ></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-admin-layout>
