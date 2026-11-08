<x-admin-layout>
    <x-slot name="title">Editar producto</x-slot>

    @include('admin.productos._form', [
        'producto' => $producto,
        'action' => route('admin.productos.update', $producto),
        'method' => 'PUT',
        'submitLabel' => 'Guardar cambios',
    ])
</x-admin-layout>
