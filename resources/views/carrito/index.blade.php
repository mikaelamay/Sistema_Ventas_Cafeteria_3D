@php
    $metodoSeleccionado = old('metodo_pago', 'efectivo');
@endphp

<x-app-layout>
    <x-slot name="title">Carrito · Cafetería</x-slot>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a
                    href="{{ route('productos.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-stone-500 transition-colors hover:text-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                    Volver al catálogo
                </a>

                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                    Tu carrito
                </h1>

                @if ($lineas->isNotEmpty())
                    <p class="mt-2 text-sm text-stone-500">
                        {{ $cantidadTotal }} {{ \Illuminate\Support\Str::plural('unidad', $cantidadTotal) }} en tu pedido
                    </p>
                @endif
            </div>

            @if ($lineas->isNotEmpty())
                <form method="POST" action="{{ route('carrito.clear') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-600 transition duration-150 hover:border-stone-300 hover:text-stone-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Vaciar carrito
                    </button>
                </form>
            @endif
        </header>

        @if ($lineas->isEmpty())
            <div class="mt-10 flex flex-col items-center justify-center rounded-2xl border border-dashed border-stone-300 bg-white/60 px-6 py-20 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-200">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21 8H6" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM18 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <h2 class="mt-6 text-xl font-semibold tracking-tight text-stone-900">
                    Tu carrito está vacío
                </h2>

                <p class="mt-2 max-w-md text-sm leading-relaxed text-stone-500">
                    Aún no has agregado productos. Explora nuestra selección y encuentra tu próxima bebida favorita.
                </p>

                <a
                    href="{{ route('productos.index') }}"
                    class="mt-7 inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-stone-50"
                >
                    Explorar el catálogo
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        @else
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
                <!-- Líneas del carrito -->
                <div class="space-y-4">
                    @foreach ($lineas as $linea)
                        @php
                            $producto = $linea['producto'];
                            $cantidad = $linea['cantidad'];
                            $sinMasStock = $cantidad >= $producto->stock;
                        @endphp

                        <article class="flex flex-col gap-4 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:p-5">
                            <a
                                href="{{ route('productos.show', $producto) }}"
                                class="block h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-stone-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                                aria-label="Ver {{ $producto->nombre }}"
                            >
                                @if ($producto->imagen_path)
                                    <img
                                        src="{{ asset('storage/'.$producto->imagen_path) }}"
                                        alt="{{ $producto->nombre }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-stone-100 via-stone-50 to-amber-50 text-stone-300">
                                        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" aria-hidden="true">
                                            <path d="M4 8h13v6a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" stroke-linejoin="round" />
                                            <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17" stroke-linejoin="round" />
                                            <path d="M8 4c0 .8.7 1.1.7 1.9M12 4c0 .8.7 1.1.7 1.9" stroke-linecap="round" />
                                        </svg>
                                    </span>
                                @endif
                            </a>

                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-base font-semibold tracking-tight text-stone-900">
                                    <a href="{{ route('productos.show', $producto) }}" class="transition-colors hover:text-amber-700">
                                        {{ $producto->nombre }}
                                    </a>
                                </h2>

                                <p class="mt-1 text-sm text-stone-500">
                                    <span class="font-medium text-stone-700">S/ {{ number_format($linea['precio_unitario'], 2) }}</span>
                                    por unidad
                                </p>

                                @if ($sinMasStock)
                                    <p class="mt-1 text-xs font-medium text-amber-700">
                                        Has alcanzado el stock disponible ({{ $producto->stock }}).
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:gap-3">
                                <!-- Control de cantidad -->
                                <div class="inline-flex items-center rounded-full border border-stone-200 bg-stone-50">
                                    <form method="POST" action="{{ route('carrito.remove') }}">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                        <input type="hidden" name="cantidad" value="1">

                                        <button
                                            type="submit"
                                            aria-label="Quitar una unidad de {{ $producto->nombre }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-stone-600 transition-colors hover:bg-white hover:text-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>

                                    <span class="min-w-8 text-center text-sm font-semibold text-stone-800" aria-live="polite">
                                        {{ $cantidad }}
                                    </span>

                                    <form method="POST" action="{{ route('carrito.add') }}">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                        <input type="hidden" name="cantidad" value="1">

                                        <button
                                            type="submit"
                                            @disabled($sinMasStock)
                                            aria-label="Agregar una unidad de {{ $producto->nombre }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-stone-600 transition-colors hover:bg-white hover:text-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 disabled:cursor-not-allowed disabled:text-stone-300 disabled:hover:bg-transparent"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 4c.414 0 .75.336.75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5c0-.414.336-.75.75-.75Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex items-center gap-4 sm:gap-5">
                                    <p class="text-base font-semibold tracking-tight text-stone-900">
                                        S/ {{ number_format($linea['subtotal'], 2) }}
                                    </p>

                                    <form method="POST" action="{{ route('carrito.remove') }}">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                                        <button
                                            type="submit"
                                            aria-label="Quitar {{ $producto->nombre }} del carrito"
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-sm font-medium text-stone-500 transition-colors hover:bg-stone-100 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                                <path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Quitar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Resumen -->
                <aside class="lg:sticky lg:top-24">
                    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold tracking-tight text-stone-900">
                            Resumen del pedido
                        </h2>

                        <dl class="mt-5 space-y-3 border-b border-stone-200 pb-5 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-stone-500">Subtotal</dt>
                                <dd class="font-medium text-stone-800">S/ {{ number_format($total, 2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-stone-500">Envío</dt>
                                <dd class="font-medium text-stone-800">Recojo en tienda</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 pt-2">
                                <dt class="text-base font-semibold text-stone-900">Total</dt>
                                <dd class="text-xl font-semibold tracking-tight text-stone-900">S/ {{ number_format($total, 2) }}</dd>
                            </div>
                        </dl>

                        <form method="POST" action="{{ route('pedidos.store') }}" class="mt-5 space-y-5">
                            @csrf

                            <fieldset>
                                <legend class="text-sm font-semibold text-stone-800">Método de pago</legend>

                                <div class="mt-3 space-y-2">
                                    @foreach ($metodosPago as $metodo)
                                        <label
                                            for="metodo-pago-{{ $metodo->value }}"
                                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm font-medium text-stone-700 transition has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-900 hover:border-stone-300"
                                        >
                                            <input
                                                id="metodo-pago-{{ $metodo->value }}"
                                                type="radio"
                                                name="metodo_pago"
                                                value="{{ $metodo->value }}"
                                                @checked($metodoSeleccionado === $metodo->value)
                                                class="h-4 w-4 border-stone-300 text-amber-600 focus:ring-2 focus:ring-amber-500 focus:ring-offset-0"
                                            >
                                            {{ ucfirst($metodo->value) }}
                                        </label>
                                    @endforeach
                                </div>

                                @error('metodo_pago')
                                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <div>
                                <label for="notas" class="block text-sm font-semibold text-stone-800">
                                    Notas <span class="font-normal text-stone-500">(opcional)</span>
                                </label>

                                <textarea
                                    id="notas"
                                    name="notas"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Indicaciones para tu pedido"
                                    class="mt-2 block w-full rounded-xl border-stone-300 bg-stone-50 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500"
                                >{{ old('notas') }}</textarea>

                                @error('notas')
                                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-amber-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                            >
                                Confirmar pedido
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>

                        <a
                            href="{{ route('productos.index') }}"
                            class="mt-3 inline-flex w-full items-center justify-center rounded-full px-6 py-3 text-sm font-semibold text-stone-600 transition-colors hover:text-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                        >
                            Seguir comprando
                        </a>
                    </div>
                </aside>
            </div>
        @endif
    </section>
</x-app-layout>
