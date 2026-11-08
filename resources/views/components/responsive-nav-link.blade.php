@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-amber-500 bg-amber-50/70 py-2 pe-4 ps-3 text-start text-base font-semibold text-amber-800 focus:border-amber-600 focus:bg-amber-100 focus:text-amber-900 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-stone-600 hover:border-stone-300 hover:bg-stone-100 hover:text-stone-900 focus:border-amber-400 focus:bg-stone-100 focus:text-stone-900 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
