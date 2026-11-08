<x-admin-layout>
    <x-slot name="title">Productos</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-stone-900">Catálogo de productos</h2>
                <p class="mt-1 text-sm text-stone-600">
                    {{ $productos->total() }} {{ $productos->total() === 1 ? 'producto' : 'productos' }} en total.
                </p>
            </div>

            <a
                href="{{ route('admin.productos.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" />
                </svg>
                Nuevo producto
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.productos.index') }}" role="search" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="buscar" class="block text-sm font-medium text-stone-700">Buscar</label>
                    <input
                        id="buscar"
                        type="search"
                        name="buscar"
                        value="{{ $buscar }}"
                        placeholder="Nombre o categoría"
                        class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-amber-500 focus:ring-amber-500"
                    >
                </div>

                <div class="sm:w-52">
                    <label for="filtro" class="block text-sm font-medium text-stone-700">Estado</label>
                    <select
                        id="filtro"
                        name="filtro"
                        class="mt-1.5 w-full rounded-xl border-stone-300 bg-white text-sm text-stone-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="todos" @selected($filtro === 'todos')>Todos</option>
                        <option value="activos" @selected($filtro === 'activos')>Activos</option>
                        <option value="inactivos" @selected($filtro === 'inactivos')>Inactivos</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-500/40"
                    >
                        Filtrar
                    </button>

                    @if ($buscar !== '' || $filtro !== 'todos')
                        <a
                            href="{{ route('admin.productos.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-stone-200 px-4 py-2.5 text-sm font-semibold text-stone-600 transition hover:border-amber-300 hover:text-amber-700"
                        >
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            @if ($productos->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M20.5 7.28v9.44a2 2 0 0 1-1 1.74l-6.5 3.75a2 2 0 0 1-2 0L4.5 18.46a2 2 0 0 1-1-1.74V7.28a2 2 0 0 1 1-1.74L11 1.79a2 2 0 0 1 2 0l6.5 3.75a2 2 0 0 1 1 1.74Z" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-stone-900">No hay productos que coincidan</h3>
                    <p class="mt-1 max-w-sm text-sm text-stone-500">
                        Prueba con otro término de búsqueda o crea un nuevo producto para el catálogo.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50/70">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                <th scope="col" class="px-6 py-3">Producto</th>
                                <th scope="col" class="px-6 py-3">Categoría</th>
                                <th scope="col" class="px-6 py-3">Precio</th>
                                <th scope="col" class="px-6 py-3">Stock</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3">Modelo 3D</th>
                                <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($productos as $producto)
                                <tr class="transition hover:bg-stone-50/60">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-stone-200 bg-stone-100">
                                                @if ($producto->imagen_path)
                                                    <img
                                                        src="{{ asset('storage/'.$producto->imagen_path) }}"
                                                        alt="{{ $producto->nombre }}"
                                                        loading="lazy"
                                                        class="h-full w-full object-cover"
                                                    >
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-stone-400">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                            <path d="M4 8h13v6a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" stroke-linejoin="round" />
                                                            <path d="M17 9h1.5a2.5 2.5 0 0 1 0 5H17" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-stone-900">{{ $producto->nombre }}</p>
                                                <p class="mt-0.5 line-clamp-1 text-xs text-stone-500">
                                                    {{ $producto->descripcion ?: 'Sin descripción' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-stone-600">
                                        {{ $producto->categoria ?: '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-stone-800">
                                        S/ {{ number_format((float) $producto->precio, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="{{ $producto->stock > 0 ? 'text-stone-700' : 'font-semibold text-red-600' }}">
                                            {{ $producto->stock }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $producto->activo ? 'bg-emerald-100 text-emerald-800 ring-emerald-200' : 'bg-stone-100 text-stone-600 ring-stone-200' }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
                                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($producto->modelo_3d_path)
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                                    <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                Sí
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-stone-400">No</span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('admin.productos.edit', $producto) }}"
                                                class="inline-flex items-center rounded-lg border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-700 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                                            >
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.productos.destroy', $producto) }}"
                                                onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.');"
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

                @if ($productos->hasPages())
                    <div class="border-t border-stone-200 px-6 py-4">
                        {{ $productos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-admin-layout>
