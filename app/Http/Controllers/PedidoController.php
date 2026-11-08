<?php

namespace App\Http\Controllers;

use App\Carrito;
use App\Enums\EstadoPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    public function __construct(private readonly Carrito $carrito) {}

    /**
     * Confirmar el pedido de forma transaccional.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'metodo_pago' => ['required', 'in:efectivo,tarjeta,transferencia'],
            'notas' => ['nullable', 'string', 'max:500'],
        ]);

        $cantidades = $this->carrito->cantidades();

        if ($cantidades === []) {
            return redirect()
                ->route('carrito.index')
                ->with('error', 'Tu carrito está vacío. Agrega productos antes de confirmar el pedido.');
        }

        try {
            $pedido = DB::transaction(function () use ($cantidades, $data): Pedido {
                $productos = Producto::query()
                    ->whereIn('id', array_keys($cantidades))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $lineas = [];
                $total = 0.0;

                foreach ($cantidades as $productoId => $cantidad) {
                    $producto = $productos->get($productoId);

                    if ($producto === null) {
                        throw ValidationException::withMessages([
                            'carrito' => 'Un producto de tu carrito ya no está disponible. Actualiza el carrito e inténtalo de nuevo.',
                        ]);
                    }

                    if (! $producto->activo) {
                        throw ValidationException::withMessages([
                            'carrito' => "El producto {$producto->nombre} ya no está disponible. Actualiza el carrito e inténtalo de nuevo.",
                        ]);
                    }

                    if ($producto->stock < $cantidad) {
                        throw ValidationException::withMessages([
                            'carrito' => "No hay stock suficiente de {$producto->nombre}. "
                                ."Disponible: {$producto->stock}.",
                        ]);
                    }

                    $precioUnitario = (float) $producto->precio;
                    $subtotal = round($precioUnitario * $cantidad, 2);

                    $lineas[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                    ];

                    $total += $subtotal;
                }

                $pedido = Pedido::create([
                    'user_id' => auth()->id(),
                    'estado' => EstadoPedido::Pendiente,
                    'metodo_pago' => $data['metodo_pago'],
                    'total' => round($total, 2),
                    'notas' => $data['notas'] ?? null,
                ]);

                $pedido->detallesPedido()->createMany($lineas);

                foreach ($cantidades as $productoId => $cantidad) {
                    $productos->get($productoId)->decrement('stock', $cantidad);
                }

                return $pedido;
            });
        } catch (ValidationException $exception) {
            $mensaje = collect($exception->errors())->flatten()->first()
                ?? 'No se pudo confirmar el pedido. Revisa tu carrito e inténtalo de nuevo.';

            return redirect()
                ->route('carrito.index')
                ->with('error', $mensaje)
                ->withErrors($exception->errors());
        }

        $this->carrito->vaciar();

        $mensaje = $pedido->user_id === null
            ? 'Pedido registrado como invitado.'
            : 'Pedido registrado correctamente.';

        return redirect()
            ->to(URL::signedRoute('pedidos.confirmado', ['pedido' => $pedido->getKey()]))
            ->with('success', $mensaje);
    }

    /**
     * Mostrar una confirmación genérica del pedido.
     *
     * La ruta está protegida por una URL firmada para que solo quien
     * completó el pedido pueda consultar número y total.
     */
    public function confirmado(int $pedido): View
    {
        $pedido = Pedido::query()->findOrFail($pedido);

        return view('pedidos.confirmado', [
            'pedido' => $pedido,
            'metodoPago' => ucfirst($pedido->metodo_pago->value),
        ]);
    }
}
