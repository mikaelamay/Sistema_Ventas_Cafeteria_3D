@php
    $editando = isset($insumo) && $insumo instanceof \App\Models\Insumo;
@endphp

<div class="mx-auto max-w-3xl space-y-6">
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

        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-stone-900">
            {{ $editando ? 'Editar insumo' : 'Nuevo insumo' }}
        </h2>
        <p class="mt-1 text-sm text-stone-600">
            {{ $editando ? 'Actualiza los datos del insumo y su stock inicial.' : 'Registra un insumo usado en la preparación de los productos.' }}
        </p>
    </div>

    @if ($errors->any())
        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            Revisa los campos marcados antes de guardar.
        </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold tracking-tight text-stone-900">Información del insumo</h3>
                    <p class="mt-0.5 text-sm text-stone-500">Nombre, unidad de medida y costos.</p>

                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-stone-700">Nombre <span class="text-red-600">*</span></label>
                            <input
                                id="nombre"
                                name="nombre"
                                type="text"
                                required
                                maxlength="255"
                                value="{{ old('nombre', $editando ? $insumo->nombre : '') }}"
                                placeholder="Ej. Café en grano"
                                @class([
                                    'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                    'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('nombre'),
                                ])
                            >
                            <x-input-error :messages="$errors->get('nombre')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="unidad_medida" class="block text-sm font-medium text-stone-700">Unidad de medida <span class="text-red-600">*</span></label>
                            <input
                                id="unidad_medida"
                                name="unidad_medida"
                                type="text"
                                required
                                maxlength="50"
                                value="{{ old('unidad_medida', $editando ? $insumo->unidad_medida : '') }}"
                                placeholder="kg, lt, unidad…"
                                @class([
                                    'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                    'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('unidad_medida'),
                                ])
                            >
                            <x-input-error :messages="$errors->get('unidad_medida')" class="mt-1.5" />
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="stock_actual" class="block text-sm font-medium text-stone-700">Stock actual <span class="text-red-600">*</span></label>
                                <input
                                    id="stock_actual"
                                    name="stock_actual"
                                    type="number"
                                    required
                                    min="0"
                                    step="0.001"
                                    inputmode="decimal"
                                    value="{{ old('stock_actual', $editando ? $insumo->stock_actual : 0) }}"
                                    placeholder="0.000"
                                    @class([
                                        'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500',
                                        'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('stock_actual'),
                                    ])
                                >
                                <x-input-error :messages="$errors->get('stock_actual')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="stock_minimo" class="block text-sm font-medium text-stone-700">Stock mínimo <span class="text-red-600">*</span></label>
                                <input
                                    id="stock_minimo"
                                    name="stock_minimo"
                                    type="number"
                                    required
                                    min="0"
                                    step="0.001"
                                    inputmode="decimal"
                                    value="{{ old('stock_minimo', $editando ? $insumo->stock_minimo : 0) }}"
                                    placeholder="0.000"
                                    @class([
                                        'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500',
                                        'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('stock_minimo'),
                                    ])
                                >
                                <x-input-error :messages="$errors->get('stock_minimo')" class="mt-1.5" />
                            </div>
                        </div>

                        <div class="sm:w-1/2">
                            <label for="costo_unitario" class="block text-sm font-medium text-stone-700">Costo unitario (S/)</label>
                            <input
                                id="costo_unitario"
                                name="costo_unitario"
                                type="number"
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                value="{{ old('costo_unitario', $editando ? $insumo->costo_unitario : '') }}"
                                placeholder="0.00"
                                @class([
                                    'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                    'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('costo_unitario'),
                                ])
                            >
                            <x-input-error :messages="$errors->get('costo_unitario')" class="mt-1.5" />
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold tracking-tight text-stone-900">Estado de stock</h3>
                    <p class="mt-0.5 text-sm text-stone-500">Cuando el stock actual llega al mínimo, se considera bajo stock.</p>

                    <div class="mt-5 flex items-start gap-3">
                        <input type="hidden" name="activo" value="0">

                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-stone-50/60 p-4 transition hover:border-amber-200 hover:bg-amber-50/40">
                            <input
                                id="activo"
                                name="activo"
                                type="checkbox"
                                value="1"
                                @checked((bool) old('activo', $editando ? $insumo->activo : true))
                                class="mt-0.5 h-4 w-4 rounded border-stone-300 text-amber-600 shadow-sm focus:ring-amber-500"
                            >
                            <span>
                                <span class="block text-sm font-semibold text-stone-800">Insumo disponible</span>
                                <span class="mt-0.5 block text-xs leading-relaxed text-stone-500">
                                    Si lo desactivas, seguirá en el panel pero no se usará en los reportes de inventario.
                                </span>
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('activo')" class="mt-1.5" />
                    </div>
                </section>
            </div>
        </div>

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
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>
