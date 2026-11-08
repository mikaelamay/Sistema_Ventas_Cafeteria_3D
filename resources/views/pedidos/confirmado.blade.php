<x-app-layout>
    <x-slot name="title">Pedido confirmado · Cafetería</x-slot>

    <section class="mx-auto max-w-2xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white text-center shadow-sm">
            <div class="border-b border-stone-100 bg-gradient-to-b from-amber-50/80 to-white px-6 pt-10 pb-8 sm:px-10">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-amber-600 ring-8 ring-amber-50">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m5 12.5 4.5 4.5L19 7.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <p class="mt-6 text-xs font-semibold uppercase tracking-[0.22em] text-amber-700">
                    Pedido confirmado
                </p>

                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-900 sm:text-4xl">
                    ¡Gracias por tu pedido!
                </h1>

                <p class="mt-3 text-sm leading-relaxed text-stone-500">
                    Hemos registrado tu pedido y ya estamos preparando todo para ti.
                </p>
            </div>

            <div class="px-6 py-8 sm:px-10">
                <dl class="grid grid-cols-1 gap-4 text-left sm:grid-cols-3">
                    <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                        <dt class="text-xs font-medium uppercase tracking-wider text-stone-500">Pedido</dt>
                        <dd class="mt-1 text-lg font-semibold text-stone-900">#{{ $pedido->id }}</dd>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                        <dt class="text-xs font-medium uppercase tracking-wider text-stone-500">Total</dt>
                        <dd class="mt-1 text-lg font-semibold text-stone-900">S/ {{ number_format((float) $pedido->total, 2) }}</dd>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                        <dt class="text-xs font-medium uppercase tracking-wider text-stone-500">Método de pago</dt>
                        <dd class="mt-1 text-lg font-semibold text-stone-900">{{ $metodoPago }}</dd>
                    </div>
                </dl>

                @if ($pedido->user_id === null)
                    <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-relaxed text-amber-900">
                        Pedido registrado como invitado. Guarda tu número de pedido para consultarlo en tienda.
                    </p>
                @endif

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a
                        href="{{ route('productos.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                    >
                        Volver al catálogo
                    </a>

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-full border border-stone-200 bg-white px-6 py-3 text-sm font-semibold text-stone-700 transition duration-150 hover:border-amber-300 hover:text-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                        >
                            Ver mi panel
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
