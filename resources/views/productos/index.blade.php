<x-app-layout>
    <x-slot name="title">Catálogo · Cafetería</x-slot>

    <section class="mx-auto max-w-7xl px-4 pt-12 pb-20 sm:px-6 lg:px-8">
        <header class="max-w-2xl">
            <span class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">
                Café de especialidad
            </span>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-stone-900 sm:text-5xl">
                Nuestro catálogo
            </h1>
            <p class="mt-4 text-base leading-relaxed text-stone-600">
                Granos seleccionados, preparaciones cuidadas y una experiencia pensada para disfrutar sin prisa.
                Explora cada bebida y descubre su detalle.
            </p>
        </header>

        @if ($productos->isEmpty())
            <div class="mt-12 flex flex-col items-center justify-center rounded-2xl border border-dashed border-stone-300 bg-white/60 px-6 py-20 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M4 8h13v6a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" stroke-linejoin="round" />
                        <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17" stroke-linejoin="round" />
                        <path d="M8 4c0 .8.7 1.1.7 1.9M12 4c0 .8.7 1.1.7 1.9" stroke-linecap="round" />
                    </svg>
                </div>
                <h2 class="mt-6 text-xl font-semibold tracking-tight text-stone-900">
                    El catálogo está en preparación
                </h2>
                <p class="mt-2 max-w-md text-sm leading-relaxed text-stone-500">
                    Estamos ajustando la selección de la casa. Vuelve pronto para descubrir nuestras bebidas.
                </p>
            </div>
        @else
            <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($productos as $producto)
                    <article class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
                        <a href="{{ route('productos.show', $producto) }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                                @if ($producto->imagen_path)
                                    <img
                                        src="{{ asset('storage/'.$producto->imagen_path) }}"
                                        alt="{{ $producto->nombre }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-stone-100 via-stone-50 to-amber-50">
                                        <svg class="h-20 w-20 text-stone-300 transition duration-500 group-hover:text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" aria-hidden="true">
                                            <path d="M4 8h13v6a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" stroke-linejoin="round" />
                                            <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17" stroke-linejoin="round" />
                                            <path d="M8 4c0 .8.7 1.1.7 1.9M12 4c0 .8.7 1.1.7 1.9" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                @endif

                                @if ($producto->categoria)
                                    <span class="absolute left-3 top-3 inline-flex items-center rounded-full border border-stone-200/80 bg-white/85 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-stone-700 backdrop-blur-sm">
                                        {{ $producto->categoria }}
                                    </span>
                                @endif

                                <span class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-semibold backdrop-blur-sm {{ $producto->stock > 0 ? 'border-emerald-200/80 bg-emerald-50/90 text-emerald-800' : 'border-stone-200/80 bg-white/90 text-stone-600' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $producto->stock > 0 ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                                    {{ $producto->stock > 0 ? 'Disponible' : 'Agotado' }}
                                </span>
                            </div>
                        </a>

                        <div class="flex flex-1 flex-col p-6">
                            <h2 class="text-lg font-semibold tracking-tight text-stone-900">
                                <a href="{{ route('productos.show', $producto) }}" class="transition-colors hover:text-amber-700">
                                    {{ $producto->nombre }}
                                </a>
                            </h2>

                            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-stone-500">
                                {{ \Illuminate\Support\Str::limit($producto->descripcion ?? 'Una selección de la casa, preparada al momento.', 120) }}
                            </p>

                            <div class="mt-auto flex items-end justify-between gap-4 pt-6">
                                <p class="text-xl font-semibold tracking-tight text-stone-900">
                                    <span class="text-sm font-medium text-stone-500">S/</span> {{ number_format((float) $producto->precio, 2) }}
                                </p>

                                <a
                                    href="{{ route('productos.show', $producto) }}"
                                    class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 transition duration-150 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2"
                                >
                                    Ver detalle
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($productos->hasPages())
                <div class="mt-12">
                    {{ $productos->links() }}
                </div>
            @endif
        @endif
    </section>
</x-app-layout>
