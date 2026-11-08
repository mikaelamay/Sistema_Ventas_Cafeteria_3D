<?php

namespace Tests\Feature;

use App\Enums\EstadoPedido;
use App\Enums\MetodoPago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmar_crea_un_pedido_pendiente_con_detalles_y_descuenta_stock(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create([
            'precio' => 12.50,
            'stock' => 10,
            'activo' => true,
        ]);

        $this->actingAs($user)->withSession([
            'carrito' => [$producto->id => 3],
        ]);

        $response = $this->post(route('pedidos.store'), [
            'metodo_pago' => 'tarjeta',
            'notas' => 'Sin azúcar',
        ]);

        $response->assertSessionHasNoErrors();

        $pedido = Pedido::sole();

        $response->assertRedirectToSignedRoute('pedidos.confirmado', ['pedido' => $pedido->id]);

        $this->assertSame($user->id, $pedido->user_id);
        $this->assertSame(EstadoPedido::Pendiente, $pedido->estado);
        $this->assertSame(MetodoPago::Tarjeta, $pedido->metodo_pago);
        $this->assertSame('37.50', $pedido->total);
        $this->assertSame('Sin azúcar', $pedido->notas);

        $detalle = $pedido->detallesPedido()->sole();

        $this->assertSame($producto->id, $detalle->producto_id);
        $this->assertSame(3, $detalle->cantidad);
        $this->assertSame('12.50', $detalle->precio_unitario);
        $this->assertSame('37.50', $detalle->subtotal);
        $this->assertSame(7, $producto->fresh()->stock);
        $this->assertNull(session('carrito'));

        $this->get(URL::signedRoute('pedidos.confirmado', ['pedido' => $pedido->id]))
            ->assertOk()
            ->assertSee('Ver mi panel')
            ->assertDontSee('Pedido registrado como invitado');
    }

    public function test_un_invitado_puede_confirmar_y_el_pedido_queda_sin_usuario(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 8.00,
            'stock' => 4,
            'activo' => true,
        ]);

        $this->withSession(['carrito' => [$producto->id => 1]]);

        $response = $this->post(route('pedidos.store'), [
            'metodo_pago' => 'efectivo',
        ]);

        $pedido = Pedido::sole();

        $response->assertRedirectToSignedRoute('pedidos.confirmado', ['pedido' => $pedido->id]);

        $this->assertNull($pedido->user_id);
        $this->assertSame(EstadoPedido::Pendiente, $pedido->estado);
        $this->assertSame('8.00', $pedido->total);
        $this->assertSame(3, $producto->fresh()->stock);

        $this->get(URL::signedRoute('pedidos.confirmado', ['pedido' => $pedido->id]))
            ->assertOk()
            ->assertSee('#'.$pedido->id)
            ->assertSee('S/ 8.00')
            ->assertSee('Efectivo')
            ->assertSee('Pedido registrado como invitado');
    }

    public function test_stock_insuficiente_no_crea_pedido_ni_descuenta_stock(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 10.00,
            'stock' => 2,
            'activo' => true,
        ]);

        $this->withSession(['carrito' => [$producto->id => 5]]);

        $response = $this->post(route('pedidos.store'), [
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertRedirect(route('carrito.index'))
            ->assertSessionHas('error', fn (string $mensaje): bool => str_contains($mensaje, $producto->nombre));

        $this->assertDatabaseCount('pedidos', 0);
        $this->assertDatabaseCount('detalle_pedido', 0);
        $this->assertSame(2, $producto->fresh()->stock);
        $this->assertSame([$producto->id => 5], session('carrito'));
    }

    public function test_un_producto_inactivo_en_el_carrito_bloquea_la_confirmacion(): void
    {
        $producto = Producto::factory()->create([
            'stock' => 10,
            'activo' => false,
        ]);

        $this->withSession(['carrito' => [$producto->id => 1]]);

        $this->post(route('pedidos.store'), [
            'metodo_pago' => 'efectivo',
        ])->assertRedirect(route('carrito.index'))
            ->assertSessionHas('error', fn (string $mensaje): bool => str_contains($mensaje, $producto->nombre));

        $this->assertDatabaseCount('pedidos', 0);
        $this->assertSame(10, $producto->fresh()->stock);
    }

    public function test_metodo_pago_invalido_falla_la_validacion(): void
    {
        $producto = Producto::factory()->create([
            'stock' => 5,
            'activo' => true,
        ]);

        $this->withSession(['carrito' => [$producto->id => 1]]);

        $this->post(route('pedidos.store'), [
            'metodo_pago' => 'bitcoin',
        ])->assertSessionHasErrors('metodo_pago');

        $this->assertDatabaseCount('pedidos', 0);
        $this->assertSame(5, $producto->fresh()->stock);
        $this->assertSame([$producto->id => 1], session('carrito'));
    }

    public function test_no_se_puede_confirmar_con_el_carrito_vacio(): void
    {
        $this->post(route('pedidos.store'), [
            'metodo_pago' => 'efectivo',
        ])->assertRedirect(route('carrito.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_la_confirmacion_requiere_una_url_firmada(): void
    {
        $pedido = Pedido::factory()->create([
            'total' => 25.50,
            'metodo_pago' => MetodoPago::Transferencia,
        ]);

        $this->get(route('pedidos.confirmado', $pedido))
            ->assertForbidden();

        $this->get(route('pedidos.confirmado', ['pedido' => 999999]))
            ->assertForbidden();

        $this->get(URL::signedRoute('pedidos.confirmado', ['pedido' => $pedido->id]))
            ->assertOk()
            ->assertSee('#'.$pedido->id)
            ->assertSee('S/ 25.50')
            ->assertSee('Transferencia');
    }
}
