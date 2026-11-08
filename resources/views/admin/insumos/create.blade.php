<x-admin-layout>
    <x-slot name="title">Nuevo insumo</x-slot>

    <div class="mx-auto max-w-3xl">
        <x-insert path="admin.insumos._form" :action="route('admin.insumos.store')" :method="'POST'" :submitLabel="'Crear insumo'" />
    </div>
</x-admin-layout>
