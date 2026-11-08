<x-admin-layout>
    <x-slot name="title">Movimiento de {{ $insumo->nombre }}</x-slot>

    <div class="mx-auto max-w-2xl">
        <div>
            <a
                href="{{ route('admin.insumos.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition hover:text-amber-700"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.47 1.47a.75.75 0 0 1 1.06 0l5 5a.75.75 0 1 1-1.06 1.06L10 3.19l-4.47 4.47a.75.75 0 1 1-1.06-1.06L8.94 3.19a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
                Volver a insumos
            </a>

            <h2 class="mt-3 text-2xl font-semibold tracking-tight text-stone-900">Registrar movimiento</h2>
            <p class="mt-1 text-sm text-stone-600">
                Stock actual: <span class="font-semibold text-stone-800">{{ number_format((float) $insumo->stock_actual, 3) }} {{ $insumo->unidad_medida }}</span>
            </p>
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                Revisa los campos marcados antes de guardar.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.insumos.movimientos.store', $insumo) }}" class="space-y-6">
            @csrf

            <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold tracking-tight text-stone-900">Tipo y cantidad</h3>
                <p class="mt-0.5 text-sm text-stone-500">Entrada suma al stock, salida lo resta y ajuste lo establece.</p>

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-stone-700">Tipo de movimiento <span class="text-red-600">*</span></label>
                        <div class="mt-1.5 grid grid-cols-1 gap-2 sm:grid-cols-3">
                            @foreach (['entrada' => 'Entrada', 'salida' => 'Salida', 'ajuste' => 'Ajuste'] as $valor => $etiqueta)
                                <label class="flex cursor-pointer items-center justify-center rounded-xl border p-3 text-sm font-medium transition
                                    {{ old('tipo', $insumo->exists ? null) === $valor ? 'bg-amber-50/50 border-amber-300 text-amber-800' : 'border-stone-300 bg-white text-stone-700 hover:border-amber-300' }}"
                                >
                                    <input
                                        id="tipo_{{ $valor }}"
                                        name="tipo"
                                        type="radio"
                                        value="{{ $valor }}"
                                        required
                                        @checked(old('tipo', null) === $valor)
                                        class="h-4 w-4 rounded border-stone-300 text-amber-600 shadow-sm focus:ring-amber-500"
                                    >
                                    <span class="ml-2">{{ $etiqueta }}</span>
                                </label>
                            @endforeach
                            <x-input-error :messages="$errors->get('tipo')" class="mt-1.5" />
                        </div>
                    </div>

                    <div>
                        <label for="cantidad" class="block text-sm font-medium text-stone-700">Cantidad <span class="text-red-600">*</span></label>
                        <div class="relative mt-1.5">
                            <input
                                id="cantidad"
                                name="cantidad"
                                type="number"
                                required
                                min="0.001"
                                step="0.001"
                                inputmode="decimal"
                                value="{{ old('cantidad') }}"
                                placeholder="0.000"
                                class="w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 pl-9
                                    {{ 'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('cantidad') }}"
                            >
                            <span class="absolute left-3 top-3.5 text-xs text-stone-500">{{ $insumo->unidad_medida }}</span>
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-1.5" />
                        </div>
                        <p class="mt-1 text-xs text-stone-500">
                            @if (old('tipo', null) === 'salida')
                                Se restará del stock actual.
                            @elseif (old('tipo', null) === 'entrada')
                                Se sumará al stock actual.
                            @else
                                Se establecerá como stock físico actual.
                            @endif
                        </p>
                    </div>

                    <div>
                        <label for="motivo" class="block text-sm font-medium text-stone-700">Motivo</label>
                        <input
                            id="motivo"
                            name="motivo"
                            type="text"
                            maxlength="255"
                            value="{{ old('motivo') }}"
                            placeholder="Reabastecimiento, ajuste de inventario, consumo de producción…"
                            class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500
                                {{ 'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('motivo') }}"
                        >
                        <x-input-error :messages="$errors->get('motivo')" class="mt-1.5" />
                    </div>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 border-t border-stone-200 pt-6 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('admin.insumos.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-5 py-2.5 text-sm font-semibold text-stone-700 transition hover:border-stone-300 hover:bg-stone-50"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2"
                >
                    Registrar movimiento
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
