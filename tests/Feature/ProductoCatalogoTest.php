<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoCatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_catalogo_solo_muestra_productos_activos(): void
    {
        $activo = Producto::factory()->create([
            'nombre' => 'Espresso de la casa',
            'activo' => true,
        ]);

        $inactivo = Producto::factory()->create([
            'nombre' => 'Producto retirado',
            'activo' => false,
        ]);

        $this->get(route('productos.index'))
            ->assertOk()
            ->assertSee($activo->nombre)
            ->assertDontSee($inactivo->nombre);
    }

    public function test_el_detalle_muestra_un_producto_activo(): void
    {
        $producto = Producto::factory()->create([
            'nombre' => 'Latte de especialidad',
            'activo' => true,
        ]);

        $this->get(route('productos.show', $producto))
            ->assertOk()
            ->assertSee($producto->nombre)
            ->assertSee('Agregar al carrito');
    }

    public function test_el_detalle_devuelve_404_para_un_producto_inactivo(): void
    {
        $producto = Producto::factory()->create([
            'activo' => false,
        ]);

        $this->get(route('productos.show', $producto))
            ->assertNotFound();
    }

    public function test_el_detalle_incluye_el_visor_3d_con_la_url_del_modelo(): void
    {
        $producto = Producto::factory()->create([
            'activo' => true,
            'modelo_3d_path' => 'productos/espresso.glb',
        ]);

        $this->get(route('productos.show', $producto))
            ->assertOk()
            ->assertSee('data-visor-3d', false)
            ->assertSee('data-modelo-url="'.asset('storage/productos/espresso.glb').'"', false)
            ->assertDontSee('Modelo 3D no disponible');
    }

    public function test_el_detalle_muestra_el_estado_vacio_cuando_no_hay_modelo(): void
    {
        $producto = Producto::factory()->create([
            'activo' => true,
            'modelo_3d_path' => null,
        ]);

        $this->get(route('productos.show', $producto))
            ->assertOk()
            ->assertSee('data-visor-3d', false)
            ->assertSee('data-modelo-url=""', false)
            ->assertSee('Modelo 3D no disponible');
    }
}
