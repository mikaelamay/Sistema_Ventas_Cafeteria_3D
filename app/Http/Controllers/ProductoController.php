<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Contracts\View\View;

class ProductoController extends Controller
{
    /**
     * Display the public product catalogue.
     */
    public function index(): View
    {
        $productos = Producto::query()
            ->where('activo', true)
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->paginate(9)
            ->withQueryString();

        return view('productos.index', [
            'productos' => $productos,
        ]);
    }

    /**
     * Display the public detail page for the given product.
     */
    public function show(Producto $producto): View
    {
        abort_unless($producto->activo, 404);

        return view('productos.show', [
            'producto' => $producto,
        ]);
    }
}
