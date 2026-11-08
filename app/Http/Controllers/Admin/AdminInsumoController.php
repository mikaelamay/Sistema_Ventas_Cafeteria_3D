<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInsumoRequest;
use App\Http\Requests\UpdateInsumoRequest;
use App\Models\Insumo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminInsumoController extends Controller
{
    /**
     * List supplies with optional search and low-stock filter.
     */
    public function index(Request $request): View
    {
        $buscar = $request->string('buscar')->trim()->toString();
        $soloBajoStock = $request->boolean('bajo_stock');

        $insumos = Insumo::query()
            ->when($buscar !== '', function ($query) use ($buscar): void {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('unidad_medida', 'like', "%{$buscar}%");
            })
            ->when($soloBajoStock, fn ($query) => $query->whereColumn('stock_actual', '<=', 'stock_minimo'))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.insumos.index', [
            'insumos' => $insumos,
            'buscar' => $buscar,
            'bajoStock' => $soloBajoStock,
        ]);
    }

    /**
     * Show the form to create a supply.
     */
    public function create(): View
    {
        return view('admin.insumos.create');
    }

    /**
     * Store a new supply.
     */
    public function store(StoreInsumoRequest $request): RedirectResponse
    {
        $insumo = $request->persist();

        return redirect()
            ->route('admin.insumos.index')
            ->with('success', "Insumo «{$insumo->nombre}» creado correctamente.");
    }

    /**
     * Show the form to edit a supply.
     */
    public function edit(Insumo $insumo): View
    {
        return view('admin.insumos.edit', [
            'insumo' => $insumo,
        ]);
    }

    /**
     * Update the supply.
     */
    public function update(UpdateInsumoRequest $request, Insumo $insumo): RedirectResponse
    {
        $insumo->update($request->validated());

        return redirect()
            ->route('admin.insumos.index')
            ->with('success', "Insumo «{$insumo->nombre}» actualizado correctamente.");
    }

    /**
     * Delete the supply when it has no movements referencing it.
     */
    public function destroy(Insumo $insumo): RedirectResponse
    {
        try {
            $insumo->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.insumos.index')
                ->with('error', 'No se puede eliminar: tiene movimientos de inventario asociados.');
        }

        return redirect()
            ->route('admin.insumos.index')
            ->with('success', "Insumo «{$insumo->nombre}» eliminado correctamente.");
    }
}
