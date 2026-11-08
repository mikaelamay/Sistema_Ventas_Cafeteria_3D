<?php

use App\Http\Controllers\Admin\AdminInsumoController;
use App\Http\Controllers\Admin\AdminMovimientoController;
use App\Http\Controllers\Admin\AdminPedidoController;
use App\Http\Controllers\Admin\AdminProductoController;
use App\Http\Controllers\Admin\AdminReporteController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductoController::class, 'index'])->name('productos.index');

Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');

Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'add'])->name('carrito.add');
Route::post('/carrito/quitar', [CarritoController::class, 'remove'])->name('carrito.remove');
Route::post('/carrito/vaciar', [CarritoController::class, 'clear'])->name('carrito.clear');

Route::post('/pedido/confirmar', [PedidoController::class, 'store'])->name('pedidos.store');
Route::get('/pedido/{pedido}/confirmado', [PedidoController::class, 'confirmado'])
    ->middleware('signed')
    ->name('pedidos.confirmado');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('productos', AdminProductoController::class)
            ->except(['show'])
            ->parameters(['productos' => 'producto']);

        Route::get('pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
        Route::patch('pedidos/{pedido}/estado', [AdminPedidoController::class, 'updateEstado'])
            ->name('pedidos.updateEstado');

        Route::resource('insumos', AdminInsumoController::class)
            ->except(['show'])
            ->parameters(['insumos' => 'insumo']);

        Route::get('insumos/{insumo}/movimientos/create', [AdminMovimientoController::class, 'create'])
            ->name('insumos.movimientos.create');
        Route::post('insumos/{insumo}/movimientos', [AdminMovimientoController::class, 'store'])
            ->name('insumos.movimientos.store');

        Route::get('reportes/ventas-periodo', [AdminReporteController::class, 'ventasPorPeriodo'])
            ->name('reportes.ventasPeriodo');
        Route::get('reportes/ventas-producto', [AdminReporteController::class, 'ventasProducto'])
            ->name('reportes.ventasProducto');
    });

require __DIR__.'/auth.php';
