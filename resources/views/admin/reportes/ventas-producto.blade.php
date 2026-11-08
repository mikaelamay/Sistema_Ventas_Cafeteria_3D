<x-admin-layout>
    <x-slot name="title">Ventas por producto</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Ranking de productos</h2>
                <p class="mt-1 text-sm text-stone-600">
                    Los productos más vendidos por cantidad y total recaudado.
                </p>
            </div>

            <a
                href="{{ route('admin.reportes.ventasPeriodo') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:border-amber-300 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5 3a4 4 0 0 0 4 4v5h5V3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1ZM4 13a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6Zm8-5a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4h1a1 1 0 0 1 1-1V3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-1h2Z" clip-rule="evenodd" />
                </svg>
                Ventas por período
            </a>
        </div>

        <section class="rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-200 bg-stone-50 px-6 py-4">
                <h3 class="text-base font-semibold tracking-tight text-stone-900">Productos más vendidos</h3>
            </div>

            <div class="p-6">
                @if ($productos->isEmpty())
                    <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                        <p class="text-sm text-stone-500">No hay ventas registradas para generar el ranking.</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-stone-100 text-sm">
                        <thead class="bg-stone-50/70">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                <th scope="col" class="px-6 py-3">Producto</th>
                                <th scope="col" class="px-6 py-3 text-right">Cantidad vendida</th>
                                <th scope="col" class="px-6 py-3 text-right">Total recaudado</th>
                                <th scope="col" class="px-6 py-3 text-right">Participación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php $maxCantidad = $productos->max('cantidad') ?: 1; $totalGeneral = $productos->sum('total'); ?>
                            @foreach ($productos as $indice => $producto)
                                <tr class="transition hover:bg-stone-50/60">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 font-semibold text-xs">
                                                {{ $indice + 1 }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-stone-900">{{ $producto['nombre'] }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-3 text-right font-medium text-stone-800">
                                        {{ $producto['cantidad'] }}
                                    </td>

                                    <td class="px-6 py-3 text-right font-medium text-stone-800">
                                        S/ {{ number_format($producto['total'], 2) }}
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        <div class="w-full h-2 rounded-full bg-stone-100 overflow-hidden">
                                            <div
                                                class="h-full bg-amber-500"
                                                style="width: {{ (float) $producto['cantidad'] / $maxCantidad * 100 }}%"
                                                role="progressbar"
                                                aria-valuenow="{{ $producto['cantidad'] }}"
                                                aria-valuemin="0"
                                                aria-valuemax="{{ $maxCantidad }}"
                                            ></div>
                                        </div>
                                        <p class="mt-1 text-right text-xs text-stone-500">
                                            {{ $totalGeneral > 0 ? number_format((float) $producto['total'] / $totalGeneral * 100, 1) : 0 }}%
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </div>
</x-admin-layout>
