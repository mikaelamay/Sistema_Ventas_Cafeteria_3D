<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class AdminProductoController extends Controller
{
    /**
     * List the products with search and status filters.
     */
    public function index(Request $request): View
    {
        $buscar = $request->string('buscar')->trim()->toString();
        $filtro = $request->string('filtro')->toString();

        if (! in_array($filtro, ['todos', 'activos', 'inactivos'], true)) {
            $filtro = 'todos';
        }

        $productos = Producto::query()
            ->when($buscar !== '', function ($query) use ($buscar): void {
                $query->where(function ($query) use ($buscar): void {
                    $query->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('categoria', 'like', "%{$buscar}%");
                });
            })
            ->when($filtro === 'activos', fn ($query) => $query->where('activo', true))
            ->when($filtro === 'inactivos', fn ($query) => $query->where('activo', false))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.productos.index', [
            'productos' => $productos,
            'buscar' => $buscar,
            'filtro' => $filtro,
        ]);
    }

    /**
     * Show the form to create a product.
     */
    public function create(): View
    {
        return view('admin.productos.create');
    }

    /**
     * Store a new product and its optional files.
     */
    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $datos = $request->safe()->only([
            'nombre',
            'descripcion',
            'precio',
            'stock',
            'categoria',
            'activo',
        ]);

        $imagen = $request->file('imagen');
        if ($imagen instanceof UploadedFile) {
            $datos['imagen_path'] = $this->guardarArchivo($imagen);
        }

        $modelo = $request->file('modelo_3d');
        if ($modelo instanceof UploadedFile) {
            $datos['modelo_3d_path'] = $this->guardarArchivo($modelo);
        }

        try {
            $producto = Producto::create($datos);
        } catch (Throwable $exception) {
            $this->eliminarArchivo($datos['imagen_path'] ?? null);
            $this->eliminarArchivo($datos['modelo_3d_path'] ?? null);

            throw $exception;
        }

        return redirect()
            ->route('admin.productos.index')
            ->with('success', "Producto «{$producto->nombre}» creado correctamente.");
    }

    /**
     * Show the form to edit a product.
     */
    public function edit(Producto $producto): View
    {
        return view('admin.productos.edit', [
            'producto' => $producto,
        ]);
    }

    /**
     * Update the product, replacing or removing its files when requested.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        $datos = $request->safe()->only([
            'nombre',
            'descripcion',
            'precio',
            'stock',
            'categoria',
            'activo',
        ]);

        $imagenAnterior = $producto->imagen_path;
        $modeloAnterior = $producto->modelo_3d_path;
        $imagenNueva = null;
        $modeloNuevo = null;

        $imagen = $request->file('imagen');
        if ($imagen instanceof UploadedFile) {
            $imagenNueva = $this->guardarArchivo($imagen);
            $datos['imagen_path'] = $imagenNueva;
        } elseif ($request->boolean('quitar_imagen')) {
            $datos['imagen_path'] = null;
        }

        $modelo = $request->file('modelo_3d');
        if ($modelo instanceof UploadedFile) {
            $modeloNuevo = $this->guardarArchivo($modelo);
            $datos['modelo_3d_path'] = $modeloNuevo;
        } elseif ($request->boolean('quitar_modelo')) {
            $datos['modelo_3d_path'] = null;
        }

        try {
            DB::transaction(fn () => $producto->update($datos));
        } catch (Throwable $exception) {
            $this->eliminarArchivo($imagenNueva);
            $this->eliminarArchivo($modeloNuevo);

            throw $exception;
        }

        if ($imagenNueva !== null || $request->boolean('quitar_imagen')) {
            $this->eliminarArchivo($imagenAnterior);
        }

        if ($modeloNuevo !== null || $request->boolean('quitar_modelo')) {
            $this->eliminarArchivo($modeloAnterior);
        }

        return redirect()
            ->route('admin.productos.index')
            ->with('success', "Producto «{$producto->nombre}» actualizado correctamente.");
    }

    /**
     * Delete the product and its files when no order references it.
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        try {
            $producto->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.productos.index')
                ->with('error', 'No se puede eliminar: tiene pedidos asociados.');
        }

        $this->eliminarArchivo($producto->imagen_path);
        $this->eliminarArchivo($producto->modelo_3d_path);

        return redirect()
            ->route('admin.productos.index')
            ->with('success', "Producto «{$producto->nombre}» eliminado correctamente.");
    }

    /**
     * Store an uploaded file on the public disk and return its relative path.
     */
    private function guardarArchivo(UploadedFile $archivo): string
    {
        $ruta = Storage::disk('public')->putFile('productos', $archivo);

        if (! is_string($ruta) || $ruta === '') {
            throw new RuntimeException('No se pudo guardar el archivo en el disco público.');
        }

        return $ruta;
    }

    /**
     * Delete a file only when it lives inside the productos directory.
     */
    private function eliminarArchivo(?string $ruta): void
    {
        if ($ruta === null || $ruta === '') {
            return;
        }

        if (! str_starts_with($ruta, 'productos/') || str_contains($ruta, '..')) {
            return;
        }

        Storage::disk('public')->delete($ruta);
    }
}
