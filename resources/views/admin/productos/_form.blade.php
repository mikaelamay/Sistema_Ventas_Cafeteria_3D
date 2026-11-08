@php
    $editando = isset($producto) && $producto instanceof \App\Models\Producto;
    $imagenActual = $editando ? $producto->imagen_path : null;
    $modeloActual = $editando ? $producto->modelo_3d_path : null;
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <a
            href="{{ route('admin.productos.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition hover:text-amber-700"
        >
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
            </svg>
            Volver a productos
        </a>

        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-stone-900">
            {{ $editando ? 'Editar producto' : 'Nuevo producto' }}
        </h2>
        <p class="mt-1 text-sm text-stone-600">
            {{ $editando ? 'Actualiza la información, la imagen o el modelo 3D del producto.' : 'Completa los datos para publicar un nuevo producto en el catálogo.' }}
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
            <!-- Main data -->
            <div class="space-y-6 lg:col-span-2">
                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold tracking-tight text-stone-900">Información general</h3>
                    <p class="mt-0.5 text-sm text-stone-500">Los datos visibles en el catálogo público.</p>

                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-stone-700">Nombre <span class="text-red-600">*</span></label>
                            <input
                                id="nombre"
                                name="nombre"
                                type="text"
                                required
                                maxlength="255"
                                value="{{ old('nombre', $editando ? $producto->nombre : '') }}"
                                placeholder="Ej. Latte de vainilla"
                                @class([
                                    'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                    'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('nombre'),
                                ])
                            >
                            <x-input-error :messages="$errors->get('nombre')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-stone-700">Descripción</label>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                                placeholder="Notas de cata, ingredientes o detalles de preparación…"
                                @class([
                                    'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                    'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('descripcion'),
                                ])
                            >{{ old('descripcion', $editando ? $producto->descripcion : '') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-1.5" />
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="categoria" class="block text-sm font-medium text-stone-700">Categoría</label>
                                <input
                                    id="categoria"
                                    name="categoria"
                                    type="text"
                                    maxlength="255"
                                    value="{{ old('categoria', $editando ? $producto->categoria : '') }}"
                                    placeholder="bebidas, postres, comida…"
                                    @class([
                                        'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                        'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('categoria'),
                                    ])
                                >
                                <x-input-error :messages="$errors->get('categoria')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="stock" class="block text-sm font-medium text-stone-700">Stock <span class="text-red-600">*</span></label>
                                <input
                                    id="stock"
                                    name="stock"
                                    type="number"
                                    required
                                    min="0"
                                    step="1"
                                    inputmode="numeric"
                                    value="{{ old('stock', $editando ? $producto->stock : 0) }}"
                                    @class([
                                        'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500',
                                        'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('stock'),
                                    ])
                                >
                                <x-input-error :messages="$errors->get('stock')" class="mt-1.5" />
                            </div>
                        </div>

                        <div class="sm:w-1/2">
                            <label for="precio" class="block text-sm font-medium text-stone-700">Precio (S/) <span class="text-red-600">*</span></label>
                            <input
                                id="precio"
                                name="precio"
                                type="number"
                                required
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                value="{{ old('precio', $editando ? $producto->precio : '') }}"
                                placeholder="0.00"
                                @class([
                                    'mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500',
                                    'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has('precio'),
                                ])
                            >
                            <x-input-error :messages="$errors->get('precio')" class="mt-1.5" />
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold tracking-tight text-stone-900">Publicación</h3>
                    <p class="mt-0.5 text-sm text-stone-500">Controla si el producto aparece en la tienda.</p>

                    <div class="mt-5">
                        <input type="hidden" name="activo" value="0">

                        <label for="activo" class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 bg-stone-50/60 p-4 transition hover:border-amber-200 hover:bg-amber-50/40">
                            <input
                                id="activo"
                                name="activo"
                                type="checkbox"
                                value="1"
                                @checked((bool) old('activo', $editando ? $producto->activo : true))
                                class="mt-0.5 h-4 w-4 rounded border-stone-300 text-amber-600 shadow-sm focus:ring-amber-500"
                            >
                            <span>
                                <span class="block text-sm font-semibold text-stone-800">Producto activo</span>
                                <span class="mt-0.5 block text-xs leading-relaxed text-stone-500">
                                    Si lo desactivas, seguirá en el panel pero no será visible en el catálogo.
                                </span>
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('activo')" class="mt-1.5" />
                    </div>
                </section>
            </div>

            <!-- Files -->
            <div class="space-y-6">
                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold tracking-tight text-stone-900">Imagen</h3>
                    <p class="mt-0.5 text-sm text-stone-500">JPG, PNG o WEBP · máx. 4 MB.</p>

                    @if ($imagenActual)
                        <div class="mt-4 overflow-hidden rounded-xl border border-stone-200 bg-stone-100">
                            <img
                                src="{{ asset('storage/'.$imagenActual) }}"
                                alt="Imagen actual de {{ $producto->nombre }}"
                                class="h-40 w-full object-cover"
                            >
                        </div>

                        <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-stone-600">
                            <input
                                type="checkbox"
                                name="quitar_imagen"
                                value="1"
                                @checked((bool) old('quitar_imagen'))
                                class="h-4 w-4 rounded border-stone-300 text-amber-600 shadow-sm focus:ring-amber-500"
                            >
                            Quitar imagen actual
                        </label>
                    @endif

                    <label for="imagen" class="mt-4 block text-sm font-medium text-stone-700">
                        {{ $imagenActual ? 'Reemplazar imagen' : 'Subir imagen' }}
                    </label>
                    <input
                        id="imagen"
                        name="imagen"
                        type="file"
                        accept="image/*"
                        class="mt-1.5 block w-full cursor-pointer rounded-xl border border-stone-300 bg-white text-sm text-stone-600 shadow-sm file:me-3 file:border-0 file:bg-stone-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-stone-700 hover:file:bg-stone-200 focus:border-amber-500 focus:ring-amber-500"
                    >
                    <p class="mt-1.5 text-xs text-stone-500">Se guarda en storage/app/public/productos.</p>
                    <x-input-error :messages="$errors->get('imagen')" class="mt-1.5" />
                </section>

                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold tracking-tight text-stone-900">Modelo 3D</h3>
                    <p class="mt-0.5 text-sm text-stone-500">Archivo .glb o .gltf · máx. 20 MB.</p>

                    @if ($modeloActual)
                        <div class="mt-4 rounded-xl border border-stone-200 bg-stone-50 p-4">
                            <p class="flex items-center gap-2 text-sm font-medium text-stone-800">
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Modelo cargado
                            </p>
                            <a
                                href="{{ asset('storage/'.$modeloActual) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-1 block truncate text-xs font-medium text-amber-700 underline-offset-2 hover:underline"
                            >
                                {{ $modeloActual }}
                            </a>
                        </div>

                        <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-stone-600">
                            <input
                                type="checkbox"
                                name="quitar_modelo"
                                value="1"
                                @checked((bool) old('quitar_modelo'))
                                class="h-4 w-4 rounded border-stone-300 text-amber-600 shadow-sm focus:ring-amber-500"
                            >
                            Quitar modelo actual
                        </label>
                    @endif

                    <label for="modelo_3d" class="mt-4 block text-sm font-medium text-stone-700">
                        {{ $modeloActual ? 'Reemplazar modelo' : 'Subir modelo' }}
                    </label>
                    <input
                        id="modelo_3d"
                        name="modelo_3d"
                        type="file"
                        accept=".glb,.gltf"
                        class="mt-1.5 block w-full cursor-pointer rounded-xl border border-stone-300 bg-white text-sm text-stone-600 shadow-sm file:me-3 file:border-0 file:bg-stone-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-stone-700 hover:file:bg-stone-200 focus:border-amber-500 focus:ring-amber-500"
                    >
                    <p class="mt-1.5 text-xs text-stone-500">El visor 3D del catálogo usará este archivo automáticamente.</p>
                    <x-input-error :messages="$errors->get('modelo_3d')" class="mt-1.5" />
                </section>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-stone-200 pt-6 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.productos.index') }}"
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
