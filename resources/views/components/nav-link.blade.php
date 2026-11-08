@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-amber-500 px-1 pt-1 text-sm font-semibold leading-5 text-stone-900 focus:outline-none focus:border-amber-600 transition duration-150 ease-in-out'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-stone-500 hover:border-stone-300 hover:text-stone-800 focus:outline-none focus:border-amber-400 focus:text-stone-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
