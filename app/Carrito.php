<?php

namespace App;

use App\Models\Producto;
use Illuminate\Support\Collection;

/**
 * Carrito de compra almacenado en la sesión del usuario.
 *
 * La estructura de la sesión es un arreglo de la forma
 * [producto_id => cantidad]. Los precios y el stock siempre se
 * resuelven contra la base de datos.
 */
class Carrito
{
    public const SESSION_KEY = 'carrito';

    /**
     * Obtener las cantidades normalizadas del carrito.
     *
     * @return array<int, int>
     */
    public function cantidades(): array
    {
        $carrito = session(self::SESSION_KEY, []);

        if (! is_array($carrito)) {
            return [];
        }

        $cantidades = [];

        foreach ($carrito as $productoId => $cantidad) {
            $productoId = (int) $productoId;
            $cantidad = (int) $cantidad;

            if ($productoId > 0 && $cantidad > 0) {
                $cantidades[$productoId] = $cantidad;
            }
        }

        return $cantidades;
    }

    /**
     * Resolver las líneas del carrito con los datos actuales de la base de datos.
     *
     * Los productos inactivos o eliminados se descartan y se purgan de la sesión.
     *
     * @return Collection<int, array{producto: Producto, cantidad: int, precio_unitario: float, subtotal: float}>
     */
    public function lineas(): Collection
    {
        $cantidades = $this->cantidades();

        if ($cantidades === []) {
            return collect();
        }

        $productos = Producto::query()
            ->whereIn('id', array_keys($cantidades))
            ->where('activo', true)
            ->get()
            ->keyBy('id');

        $this->purgarNoDisponibles($cantidades, $productos->keys()->all());

        return $productos
            ->map(function (Producto $producto) use ($cantidades): array {
                $cantidad = $cantidades[$producto->id];
                $precioUnitario = (float) $producto->precio;

                return [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => round($precioUnitario * $cantidad, 2),
                ];
            })
            ->values();
    }

    /**
     * Agregar unidades de un producto al carrito.
     */
    public function agregar(int $productoId, int $cantidad): void
    {
        if ($productoId <= 0 || $cantidad <= 0) {
            return;
        }

        $cantidades = $this->cantidades();
        $cantidades[$productoId] = ($cantidades[$productoId] ?? 0) + $cantidad;

        session()->put(self::SESSION_KEY, $cantidades);
    }

    /**
     * Quitar unidades de un producto. Sin cantidad elimina la línea completa.
     */
    public function quitar(int $productoId, ?int $cantidad = null): void
    {
        $cantidades = $this->cantidades();

        if (! array_key_exists($productoId, $cantidades)) {
            return;
        }

        if ($cantidad === null) {
            unset($cantidades[$productoId]);
        } else {
            $restante = $cantidades[$productoId] - $cantidad;

            if ($restante > 0) {
                $cantidades[$productoId] = $restante;
            } else {
                unset($cantidades[$productoId]);
            }
        }

        session()->put(self::SESSION_KEY, $cantidades);
    }

    /**
     * Vaciar por completo el carrito.
     */
    public function vaciar(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Eliminar de la sesión los productos que ya no están disponibles.
     *
     * @param  array<int, int>  $cantidades
     * @param  array<int, int>  $productosValidos
     */
    private function purgarNoDisponibles(array $cantidades, array $productosValidos): void
    {
        $validos = array_intersect_key($cantidades, array_flip($productosValidos));

        if (count($validos) !== count($cantidades)) {
            session()->put(self::SESSION_KEY, $validos);
        }
    }
}
