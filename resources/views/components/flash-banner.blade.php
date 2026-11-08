@php
    $mensajeExito = session('success');
    $mensajeError = session('error') ?? ($errors->any() ? $errors->first() : null);
@endphp

@if ($mensajeExito || $mensajeError)
    <div class="mx-auto max-w-7xl space-y-3 px-4 pt-6 sm:px-6 lg:px-8">
        @if ($mensajeExito)
            <div
                role="status"
                aria-live="polite"
                class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm"
            >
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>

                <p class="leading-relaxed">{{ $mensajeExito }}</p>
            </div>
        @endif

        @if ($mensajeError)
            <div
                role="alert"
                class="flex items-start gap-3 rounded-2xl border border-stone-300 bg-stone-100 px-4 py-3 text-sm text-stone-800 shadow-sm"
            >
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>

                <p class="leading-relaxed">{{ $mensajeError }}</p>
            </div>
        @endif
    </div>
@endif
