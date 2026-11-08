<?php

namespace Tests\Feature\Admin;

use App\Enums\EstadoPedido;
use App\Enums\MetodoPago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $pedido = Pedido::factory()->create();

        $this->get(route('admin.pedidos.index'))->assertRedirect(route('login'));
        $this->patch(route('admin.pedidos.updateEstado', $pedido), ['estado' => 'entregado'])
            ->assertRedirect(route('login'));

        $this->assertSame(EstadoPedido::Pendiente, $pedido->fresh()->estado);
    }

    public function test_customer_receives_403(): void
    {
        $cliente = User::factory()->cliente()->create();
        $pedido = Pedido::factory()->create();

        $this->actingAs($cliente)->get(route('admin.pedidos.index'))->assertForbidden();

        $this->actingAs($cliente)
            ->patch(route('admin.pedidos.updateEstado', $pedido), ['estado' => 'entregado'])
            ->assertForbidden();

        $this->assertSame(EstadoPedido::Pendiente, $pedido->fresh()->estado);
    }

    public function test_admin_sees_orders_newest_first_with_lines_and_readable_data(): void
    {
        $admin = $this->admin();
        $cliente = User::factory()->cliente()->create(['name' => 'Ana Torres']);
        $producto = Producto::factory()->create(['nombre' => 'Espresso doble']);

        $antiguo = Pedido::factory()->create([
            'user_id' => null,
            'estado' => EstadoPedido::Entregado,
            'total' => 8.00,
            'metodo_pago' => MetodoPago::Efectivo,
            'created_at' => now()->subDays(2),
        ]);

        $reciente = Pedido::factory()->create([
            'user_id' => $cliente->id,
            'estado' => EstadoPedido::EnPreparacion,
            'total' => 25.50,
            'metodo_pago' => MetodoPago::Tarjeta,
            'notas' => 'Sin azúcar',
            'created_at' => now(),
        ]);

        $reciente->detallesPedido()->create([
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 12.75,
            'subtotal' => 25.50,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pedidos.index'));

        $response
            ->assertOk()
            ->assertSee('Gestión de pedidos')
            ->assertSee('#'.$reciente->id)
            ->assertSee('#'.$antiguo->id)
            ->assertSee('Ana Torres')
            ->assertSee('Invitado')
            ->assertSee('En preparación')
            ->assertSee('Entregado')
            ->assertSee('S/ 25.50')
            ->assertSee('Tarjeta')
            ->assertSee('Espresso doble')
            ->assertSee('Sin azúcar');

        $response->assertViewHas('pedidos', fn ($pedidos): bool => $pedidos->first()->is($reciente));
    }

    public function test_index_can_be_filtered_by_state(): void
    {
        $admin = $this->admin();

        $pendiente = Pedido::factory()->create([
            'estado' => EstadoPedido::Pendiente,
            'created_at' => now()->subHour(),
        ]);

        $entregado = Pedido::factory()->create([
            'estado' => EstadoPedido::Entregado,
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pedidos.index', ['estado' => 'pendiente']))
            ->assertOk()
            ->assertViewHas('pedidos', fn ($pedidos): bool => $pedidos->total() === 1 && $pedidos->first()->is($pendiente));

        $this->actingAs($admin)
            ->get(route('admin.pedidos.index', ['estado' => 'entregado']))
            ->assertOk()
            ->assertViewHas('pedidos', fn ($pedidos): bool => $pedidos->total() === 1 && $pedidos->first()->is($entregado));
    }

    public function test_existing_listo_and_cancelado_states_are_displayed(): void
    {
        $admin = $this->admin();

        Pedido::factory()->create(['estado' => EstadoPedido::Listo]);
        Pedido::factory()->create(['estado' => EstadoPedido::Cancelado]);

        $this->actingAs($admin)
            ->get(route('admin.pedidos.index'))
            ->assertOk()
            ->assertSee('Listo')
            ->assertSee('Cancelado');
    }

    public function test_update_estado_changes_only_the_status(): void
    {
        $admin = $this->admin();
        $cliente = User::factory()->cliente()->create();

        $pedido = Pedido::factory()->create([
            'user_id' => $cliente->id,
            'estado' => EstadoPedido::Pendiente,
            'total' => 42.00,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.pedidos.updateEstado', $pedido), [
                'estado' => 'en_preparacion',
                'total' => 9999,
                'user_id' => null,
            ])
            ->assertRedirect(route('admin.pedidos.index'))
            ->assertSessionHas('success');

        $pedido->refresh();

        $this->assertSame(EstadoPedido::EnPreparacion, $pedido->estado);
        $this->assertSame('42.00', $pedido->total);
        $this->assertSame($cliente->id, $pedido->user_id);
    }

    public function test_update_estado_accepts_only_the_three_allowed_values(): void
    {
        $admin = $this->admin();
        $pedido = Pedido::factory()->create(['estado' => EstadoPedido::Pendiente]);

        foreach (['listo', 'cancelado', 'desconocido'] as $estado) {
            $this->actingAs($admin)
                ->patch(route('admin.pedidos.updateEstado', $pedido), ['estado' => $estado])
                ->assertSessionHasErrors('estado');
        }

        $this->assertSame(EstadoPedido::Pendiente, $pedido->fresh()->estado);
    }

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }
}
