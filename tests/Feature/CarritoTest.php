<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarritoTest extends TestCase
{
    use RefreshDatabase;

    public function test_agregar_un_producto_actualiza_la_sesion_y_el_contador(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 12.50,
            'stock' => 5,
            'activo' => true,
        ]);

        $response = $this->from(route('productos.show', $producto))
            ->post(route('carrito.add'), [
                'producto_id' => $producto->id,
                'cantidad' => 2,
            ]);

        $response->assertRedirect(route('productos.show', $producto))
            ->assertSessionHas('success');

        $this->assertSame([$producto->id => 2], session('carrito'));

        $this->get(route('productos.index'))
            ->assertOk()
            ->assertSee('data-carrito-contador="2"', false);
    }

    public function test_no_permite_agregar_mas_unidades_de_las_disponibles(): void
    {
        $producto = Producto::factory()->create([
            'stock' => 3,
            'activo' => true,
        ]);

        $this->from(route('productos.show', $producto))
            ->post(route('carrito.add'), [
                'producto_id' => $producto->id,
                'cantidad' => 2,
            ]);

        $response = $this->from(route('productos.show', $producto))
            ->post(route('carrito.add'), [
                'producto_id' => $producto->id,
                'cantidad' => 2,
            ]);

        $response->assertRedirect(route('productos.show', $producto))
            ->assertSessionHas('error');

        $this->assertSame([$producto->id => 2], session('carrito'));
    }

    public function test_rechaza_un_producto_inactivo(): void
    {
        $producto = Producto::factory()->create([
            'stock' => 10,
            'activo' => false,
        ]);

        $response = $this->from(route('productos.index'))
            ->post(route('carrito.add'), [
                'producto_id' => $producto->id,
                'cantidad' => 1,
            ]);

        $response->assertRedirect(route('productos.index'))
            ->assertSessionHas('error');

        $this->assertArrayNotHasKey($producto->id, session('carrito', []));
    }

    public function test_la_cantidad_debe_ser_al_menos_uno(): void
    {
        $producto = Producto::factory()->create([
            'stock' => 10,
            'activo' => true,
        ]);

        $response = $this->post(route('carrito.add'), [
            'producto_id' => $producto->id,
            'cantidad' => 0,
        ]);

        $response->assertSessionHasErrors('cantidad');

        $this->assertArrayNotHasKey($producto->id, session('carrito', []));
    }

    public function test_quitar_una_unidad_y_eliminar_la_linea_completa(): void
    {
        $producto = Producto::factory()->create();

        $this->withSession(['carrito' => [$producto->id => 3]]);

        $this->post(route('carrito.remove'), [
            'producto_id' => $producto->id,
            'cantidad' => 1,
        ])->assertRedirect(route('carrito.index'));

        $this->assertSame([$producto->id => 2], session('carrito'));

        $this->post(route('carrito.remove'), [
            'producto_id' => $producto->id,
        ])->assertRedirect(route('carrito.index'));

        $this->assertSame([], session('carrito'));
    }

    public function test_vaciar_el_carrito(): void
    {
        $producto = Producto::factory()->create();

        $this->withSession(['carrito' => [$producto->id => 2]]);

        $this->post(route('carrito.clear'))
            ->assertRedirect(route('carrito.index'))
            ->assertSessionHas('success');

        $this->assertNull(session('carrito'));
    }

    public function test_el_carrito_ignora_productos_inactivos_o_eliminados(): void
    {
        $activo = Producto::factory()->create([
            'nombre' => 'Latte disponible',
            'stock' => 5,
            'activo' => true,
        ]);

        $inactivo = Producto::factory()->create([
            'nombre' => 'Producto retirado',
            'stock' => 5,
            'activo' => false,
        ]);

        $this->withSession([
            'carrito' => [
                $activo->id => 2,
                $inactivo->id => 1,
                999999 => 4,
            ],
        ]);

        $this->get(route('carrito.index'))
            ->assertOk()
            ->assertSee($activo->nombre)
            ->assertDontSee($inactivo->nombre);

        $this->assertSame([$activo->id => 2], session('carrito'));
    }

    public function test_el_contador_del_navbar_suma_las_cantidades(): void
    {
        $primerProducto = Producto::factory()->create(['activo' => true]);
        $segundoProducto = Producto::factory()->create(['activo' => true]);

        $this->withSession([
            'carrito' => [
                $primerProducto->id => 2,
                $segundoProducto->id => 3,
            ],
        ]);

        $this->get(route('productos.index'))
            ->assertOk()
            ->assertSee('data-carrito-contador="5"', false);
    }
}
