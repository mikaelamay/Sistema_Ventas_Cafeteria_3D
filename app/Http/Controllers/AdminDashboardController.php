<?php

namespace App\Http\Controllers;

use App\Enums\EstadoPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the administration dashboard with real metrics.
     */
    public function __invoke(): View
    {
        $resumenHoy = Pedido::query()
            ->whereDate('created_at', today())
            ->selectRaw(
                'COUNT(*) as pedidos, COALESCE(SUM(CASE WHEN estado <> ? THEN total ELSE 0 END), 0) as ventas',
                [EstadoPedido::Cancelado->value],
            )
            ->first();

        $metricas = [
            'productos_activos' => Producto::query()->where('activo', true)->count(),
            'pedidos_pendientes' => Pedido::query()->where('estado', EstadoPedido::Pendiente)->count(),
            'pedidos_hoy' => (int) ($resumenHoy->pedidos ?? 0),
            'ventas_hoy' => (float) ($resumenHoy->ventas ?? 0),
        ];

        $ultimosPedidos = Pedido::query()
            ->with('user')
            ->latest('created_at')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'metricas' => $metricas,
            'ultimosPedidos' => $ultimosPedidos,
        ]);
    }
}
