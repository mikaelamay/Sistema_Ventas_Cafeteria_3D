@props(['estado'])

@php
    $valor = $estado instanceof \App\Enums\EstadoPedido ? $estado->value : (string) $estado;

    $etiquetas = [
        'pendiente' => 'Pendiente',
        'en_preparacion' => 'En preparación',
        'listo' => 'Listo',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];

    $estilos = [
        'pendiente' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'en_preparacion' => 'bg-blue-100 text-blue-800 ring-blue-200',
        'listo' => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
        'entregado' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'cancelado' => 'bg-stone-200 text-stone-700 ring-stone-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset '.($estilos[$valor] ?? 'bg-stone-100 text-stone-700 ring-stone-200')]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    {{ $etiquetas[$valor] ?? ucfirst(str_replace('_', ' ', $valor)) }}
</span>
