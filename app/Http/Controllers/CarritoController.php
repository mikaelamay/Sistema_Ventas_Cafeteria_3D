<?php

namespace App\Http\Controllers;

use App\Carrito;
use App\Enums\MetodoPago;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CarritoController extends Controller
{
    public function __construct(private readonly Carrito $carrito) {}

    /**
     * Mostrar el carrito con los precios actuales de la base de datos.
     */
    public function index(): View
    {
        $lineas = $this->carrito->lineas();

        return view('carrito.index', [
            'lineas' => $lineas,
            'total' => round((float) $lineas->sum('subtotal'), 2),
            'cantidadTotal' => $lineas->sum('cantidad'),
            'metodosPago' => MetodoPago::cases(),
        ]);
    }

    /**
     * Agregar un producto al carrito verificando disponibilidad y stock.
     */
    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $producto = Producto::query()->find($data['producto_id']);

        if ($producto === null || ! $producto->activo) {
            return back()->with('error', 'Este producto ya no está disponible.');
        }

        $cantidad = (int) $data['cantidad'];
        $enCarrito = $this->carrito->cantidades()[$producto->id] ?? 0;
        $disponible = $producto->stock - $enCarrito;

        if ($producto->stock <= 0 || $disponible <= 0) {
            return back()->with(
                'error',
                "{$producto->nombre} está agotado o ya no queda stock disponible para agregar."
            );
        }

        if ($cantidad > $disponible) {
            return back()->with(
                'error',
                "No hay stock suficiente de {$producto->nombre}. Puedes agregar hasta {$disponible} "
                .Str::plural('unidad', $disponible).'.'
            );
        }

        $this->carrito->agregar($producto->id, $cantidad);

        return back()->with(
            'success',
            "{$producto->nombre} se agregó al carrito."
        );
    }

    /**
     * Quitar una línea completa o restar unidades de un producto.
     */
    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'producto_id' => ['required', 'integer'],
            'cantidad' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->carrito->quitar(
            (int) $data['producto_id'],
            isset($data['cantidad']) ? (int) $data['cantidad'] : null
        );

        return redirect()
            ->route('carrito.index')
            ->with('success', isset($data['cantidad'])
                ? 'Cantidad actualizada en el carrito.'
                : 'Producto eliminado del carrito.');
    }

    /**
     * Vaciar el carrito por completo.
     */
    public function clear(): RedirectResponse
    {
        $this->carrito->vaciar();

        return redirect()
            ->route('carrito.index')
            ->with('success', 'Tu carrito está vacío.');
    }
}
