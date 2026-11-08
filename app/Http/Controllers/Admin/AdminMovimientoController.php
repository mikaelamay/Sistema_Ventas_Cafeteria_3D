<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovimientoRequest;
use App\Models\Insumo;
use App\Models\MovimientoInventario;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminMovimientoController extends Controller
{
    /**
     * Show the form to register a movement for a supply.
     */
    public function create(Insumo $insumo): View
    {
        return view('admin.insumos.movimientos.create', [
            'insumo' => $insumo,
        ]);
    }

    /**
     * Register a movement and update the supply stock atomically.
     */
    public function store(StoreMovimientoRequest $request, Insumo $insumo): RedirectResponse
    {
        $datos = $request->validated();
        $tipo = $datos['tipo'];
        $cantidad = (float) $datos['cantidad'];

        // Validar stock suficiente antes de la transacción (salida).
        if ($tipo === 'salida' && $insumo->stock_actual - $cantidad < 0) {
            return back()
                ->withInput()
                ->withErrors(['cantidad' => 'No hay suficiente stock para registrar esta salida.'])
                ->with('error', 'No se pudo registrar el movimiento.');
        }

        DB::transaction(function () use ($insumo, $tipo, $cantidad, $datos, $request): void {
            $nuevoStock = match ($tipo) {
                'entrada' => $insumo->stock_actual + $cantidad,
                'salida' => $insumo->stock_actual - $cantidad,
                'ajuste' => $cantidad,
                default => $insumo->stock_actual,
            };

            MovimientoInventario::create([
                'insumo_id' => $insumo->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'motivo' => $datos['motivo'] ?? null,
                'user_id' => $request->user()?->id,
            ]);

            $insumo->update(['stock_actual' => $nuevoStock]);
        });

        $nombreTipo = match ($tipo) {
            'entrada' => 'entrada',
            'salida' => 'salida',
            'ajuste' => 'ajuste',
            default => 'movimiento',
        };

        return redirect()
            ->route('admin.insumos.index')
            ->with('success', "Se registró una {$nombreTipo} de {$cantidad} {$insumo->unidad_medida} en «{$insumo->nombre}».");
    }
}
