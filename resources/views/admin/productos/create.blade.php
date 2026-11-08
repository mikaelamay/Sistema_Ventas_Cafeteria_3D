<x-admin-layout>
    <x-slot name="title">Nuevo producto</x-slot>

    @include('admin.productos._form', [
        'producto' => null,
        'action' => route('admin.productos.store'),
        'method' => 'POST',
        'submitLabel' => 'Crear producto',
    ])
</x-admin-layout>
