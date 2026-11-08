<x-admin-layout>
    <x-slot name="title">Editar insumo</x-slot>

    <div class="mx-auto max-w-3xl">
        <x-insert path="admin.insumos._form" :action="route('admin.insumos.update', $insumo)" :method="'PUT'" :submitLabel="'Guardar cambios'" />
    </div>
</x-admin-layout>
