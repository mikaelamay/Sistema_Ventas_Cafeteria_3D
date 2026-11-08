<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminReporteController extends Controller
{
    /**
     * Sales report by period with daily evolution.
     */
    public function ventasPorPeriodo(Request $request): View
    {
        $inicio = $request->date('fecha_inicio') ? Carbon::parse($request->date('fecha_inicio'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
        $fin = $request->date('fecha_fin') ? Carbon::parse($request->date('fecha_fin'))->endOfDay() : Carbon::now()->endOfDay();

        if ($fin < $inicio) {
            $fin = $inicio->copy()->endOfDay();
        }

        $pedidos = Pedido::query()
            ->whereBetween('created_at', [$inicio, $fin])
            ->where('estado', '<>', 'cancelado')
            ->orderBy('created_at');

        $totalVendido = (clone $pedidos)->sum('total');
        $numeroPedidos = (clone $pedidos)->count();

        $evolucion = (clone $pedidos)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as pedidos, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(fn ($fila) => [
                'fecha' => Carbon::parse($fila->fecha)->format('d/M'),
                'pedidos' => (int) $fila->pedidos,
                'total' => (float) $fila->total,
            ]);

        return view('admin.reportes.ventas-periodo', [
            'fechaInicio' => $inicio->format('Y-m-d'),
            'fechaFin' => $fin->format('Y-m-d'),
            'totalVendido' => $totalVendido,
            'numeroPedidos' => $numeroPedidos,
            'evolucion' => $evolucion,
        ]);
    }

    /**
     * Sales report by product: top selling products ranking.
     */
    public function ventasProducto(Request $request): View
    {
        $productos = DetallePedido::query()
            ->join('pedidos', 'detalle_pedido.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', '<>', 'cancelado')
            ->selectRaw('producto_id, SUM(cantidad) as cantidad_vendida, SUM(subtotal) as total_recaudado')
            ->groupBy('producto_id')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get()
            ->map(fn ($fila) => [
                'nombre' => optional(Producto::find($fila->producto_id))->nombre ?? 'Producto #'.$fila->producto_id,
                'cantidad' => (int) $fila->cantidad_vendida,
                'total' => (float) $fila->total_recaudado,
            ]);

        return view('admin.reportes.ventas-producto', [
            'productos' => $productos,
        ]);
    }
}
