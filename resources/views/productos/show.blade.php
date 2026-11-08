@php
    $modeloUrl = $producto->modelo_3d_path ? asset('storage/'.$producto->modelo_3d_path) : '';
    $disponible = $producto->stock > 0;
    $maxCantidad = min($producto->stock, 10);
@endphp

<x-app-layout>
    <x-slot name="title">{{ $producto->nombre }} · Cafetería</x-slot>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-16">
        <a
            href="{{ route('productos.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition-colors hover:text-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
        >
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
            </svg>
            Volver al catálogo
        </a>

        <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">
            <!-- 3D viewer -->
            <div>
                <div
                    data-visor-contenedor
                    class="relative aspect-square w-full overflow-hidden rounded-2xl border border-stone-200 bg-stone-100"
                >
                    <canvas
                        data-visor-3d
                        data-modelo-url="{{ $modeloUrl }}"
                        data-modelo-alt="{{ $producto->nombre }}"
                        @if ($modeloUrl) tabindex="0" @endif
                        @if (! $modeloUrl) aria-hidden="true" @endif
                        class="absolute inset-0 h-full w-full cursor-grab opacity-0 transition-opacity duration-500 active:cursor-grabbing focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-amber-500 {{ $modeloUrl ? '' : 'pointer-events-none' }}"
                        role="img"
                        aria-label="Modelo 3D interactivo de {{ $producto->nombre }}. Arrastra para rotar y usa la rueda o la pinza para hacer zoom."
                        @if ($modeloUrl) aria-describedby="visor-ayuda" @endif
                    >Tu navegador no puede mostrar el modelo 3D interactivo.</canvas>

                    @if ($modeloUrl)
                        <!-- Rotation hint -->
                        <span data-visor-pista class="pointer-events-none absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full border border-stone-200/80 bg-white/85 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-stone-600 backdrop-blur-sm">
                            <svg class="h-3.5 w-3.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path d="M20 12a8 8 0 1 1-2.34-5.66M20 4v4h-4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Vista 3D
                        </span>

                        <!-- Loading skeleton -->
                        <div data-visor-cargando class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-stone-100/90">
                            <span class="h-9 w-9 animate-spin rounded-full border-2 border-stone-300 border-t-amber-600" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-stone-500">Cargando modelo 3D…</p>
                        </div>
                    @else
                        <!-- Empty state: no model uploaded yet -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center px-6 text-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/70 text-stone-400 shadow-sm ring-1 ring-stone-200">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M4 8h13v6a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" stroke-linejoin="round" />
                                    <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17" stroke-linejoin="round" />
                                    <path d="M8 4c0 .8.7 1.1.7 1.9M12 4c0 .8.7 1.1.7 1.9" stroke-linecap="round" />
                                </svg>
                            </div>
                            <h2 class="mt-5 text-lg font-semibold tracking-tight text-stone-800">
                                Modelo 3D no disponible
                            </h2>
                            <p class="mt-1.5 max-w-xs text-sm leading-relaxed text-stone-500">
                                Estamos preparando una vista tridimensional de este producto. Mientras tanto, descubre todos sus detalles en esta ficha.
                            </p>
                        </div>
                    @endif

                    <!-- Load error -->
                    <div data-visor-error class="absolute inset-0 hidden flex-col items-center justify-center px-6 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/70 text-stone-400 shadow-sm ring-1 ring-stone-200">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h2 class="mt-5 text-lg font-semibold tracking-tight text-stone-800">
                            No se pudo cargar el modelo 3D
                        </h2>
                        <p class="mt-1.5 max-w-xs text-sm leading-relaxed text-stone-500">
                            El archivo no está disponible en este momento. Recarga la página o inténtalo más tarde.
                        </p>
                    </div>
                </div>

                @if ($modeloUrl)
                    <p id="visor-ayuda" data-visor-ayuda class="mt-3 text-center text-xs leading-relaxed text-stone-500">
                        Arrastra para rotar · Rueda o pinza para hacer zoom
                    </p>
                @endif
            </div>

            <!-- Product information -->
            <div class="flex flex-col lg:py-4">
                @if ($producto->categoria)
                    <span class="inline-flex w-fit items-center rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-800 ring-1 ring-amber-200">
                        {{ $producto->categoria }}
                    </span>
                @endif

                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                    {{ $producto->nombre }}
                </h1>

                <p class="mt-5 text-base leading-relaxed text-stone-600">
                    {{ $producto->descripcion ?? 'Una selección de la casa, preparada al momento con ingredientes cuidadosamente elegidos.' }}
                </p>

                <div class="mt-8 flex items-center gap-3">
                    <p class="text-4xl font-semibold tracking-tight text-stone-900">
                        <span class="text-lg font-medium text-stone-500">S/</span> {{ number_format((float) $producto->precio, 2) }}
                    </p>
                </div>

                <div class="mt-6 flex items-center gap-2 text-sm">
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 font-medium {{ $disponible ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-stone-200 bg-stone-100 text-stone-600' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $disponible ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                        {{ $disponible ? 'Disponible' : 'Agotado' }}
                    </span>

                    @if ($disponible)
                        <span class="text-stone-500">
                            {{ $producto->stock }} {{ \Illuminate\Support\Str::plural('unidad', $producto->stock) }} en stock
                        </span>
                    @else
                        <span class="text-stone-500">Vuelve pronto para probarlo</span>
                    @endif
                </div>

                <div class="mt-10 border-t border-stone-200 pt-8">
                    @if ($disponible)
                        <form method="POST" action="{{ route('carrito.add') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            @csrf
                            <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                            <div class="sm:w-28">
                                <label for="cantidad" class="block text-xs font-semibold uppercase tracking-wider text-stone-500">
                                    Cantidad
                                </label>

                                <input
                                    id="cantidad"
                                    name="cantidad"
                                    type="number"
                                    min="1"
                                    max="{{ $maxCantidad }}"
                                    value="{{ old('cantidad', 1) }}"
                                    inputmode="numeric"
                                    required
                                    aria-describedby="carrito-nota"
                                    class="mt-2 block w-full rounded-xl border-stone-300 bg-white text-center text-sm font-semibold text-stone-800 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                >
                            </div>

                            <button
                                type="submit"
                                aria-describedby="carrito-nota"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-amber-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50 sm:flex-none sm:px-10"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21 8H6" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Agregar al carrito
                            </button>
                        </form>

                        <p id="carrito-nota" class="mt-3 text-xs leading-relaxed text-stone-500">
                            Puedes agregar hasta {{ $maxCantidad }} {{ \Illuminate\Support\Str::plural('unidad', $maxCantidad) }} por pedido.
                            @error('cantidad')
                                <span class="mt-1 block font-medium text-red-700">{{ $message }}</span>
                            @enderror
                        </p>
                    @else
                        <button
                            type="button"
                            disabled
                            class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-full bg-stone-200 px-6 py-3.5 text-sm font-semibold text-stone-500 shadow-none sm:w-auto sm:px-10"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21 8H6" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Agotado
                        </button>

                        <p class="mt-3 text-xs leading-relaxed text-stone-500">
                            Este producto no está disponible por el momento. Vuelve pronto para probarlo.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
