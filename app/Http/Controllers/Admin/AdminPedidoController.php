<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoPedido;
use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPedidoController extends Controller
{
    /**
     * List the orders with their lines, newest first.
     */
    public function index(Request $request): View
    {
        $filtro = $request->string('estado')->toString();
        $estadosValidos = array_map(fn (EstadoPedido $estado): string => $estado->value, EstadoPedido::cases());

        if (! in_array($filtro, $estadosValidos, true)) {
            $filtro = '';
        }

        $pedidos = Pedido::query()
            ->with(['user', 'detallesPedido.producto'])
            ->when($filtro !== '', fn ($query) => $query->where('estado', $filtro))
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pedidos.index', [
            'pedidos' => $pedidos,
            'filtro' => $filtro,
            'estados' => EstadoPedido::cases(),
        ]);
    }

    /**
     * Update only the order status from the admin panel.
     */
    public function updateEstado(Request $request, Pedido $pedido): RedirectResponse
    {
        $data = $request->validate([
            'estado' => ['required', 'string', Rule::in(['pendiente', 'en_preparacion', 'entregado'])],
        ]);

        $pedido->update(['estado' => $data['estado']]);

        return redirect()
            ->route('admin.pedidos.index')
            ->with('success', "Pedido #{$pedido->id} actualizado correctamente.");
    }
}
