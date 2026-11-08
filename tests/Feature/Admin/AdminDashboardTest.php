<?php

namespace Tests\Feature\Admin;

use App\Enums\EstadoPedido;
use App\Enums\MetodoPago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }

    public function test_customer_receives_403(): void
    {
        $user = User::factory()->cliente()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_the_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Panel de administración');
    }

    public function test_dashboard_shows_real_metrics_and_recent_orders(): void
    {
        $admin = User::factory()->admin()->create();
        $cliente = User::factory()->cliente()->create(['name' => 'Ana Torres']);

        Producto::factory()->count(2)->create(['activo' => true]);
        Producto::factory()->create(['activo' => false]);

        Pedido::factory()->create([
            'estado' => EstadoPedido::Pendiente,
            'total' => 30.00,
            'created_at' => now(),
        ]);

        Pedido::factory()->create([
            'user_id' => $cliente->id,
            'estado' => EstadoPedido::Entregado,
            'total' => 20.00,
            'metodo_pago' => MetodoPago::Tarjeta,
            'created_at' => now(),
        ]);

        Pedido::factory()->create([
            'estado' => EstadoPedido::Cancelado,
            'total' => 99.00,
            'created_at' => now(),
        ]);

        Pedido::factory()->create([
            'estado' => EstadoPedido::Pendiente,
            'total' => 50.00,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();

        $response->assertViewHas('metricas', fn (array $metricas): bool => $metricas === [
            'productos_activos' => 2,
            'pedidos_pendientes' => 2,
            'pedidos_hoy' => 3,
            'ventas_hoy' => 50.0,
        ]);

        $response
            ->assertSee('Nuevo producto')
            ->assertSee('Ver pedidos')
            ->assertSee('Ana Torres')
            ->assertSee('S/ 50.00');
    }

    public function test_admin_panel_link_is_only_visible_for_administrators(): void
    {
        $this->get(route('productos.index'))->assertOk()->assertDontSee('Panel de Admin');

        $cliente = User::factory()->cliente()->create();
        $this->actingAs($cliente)->get(route('productos.index'))->assertOk()->assertDontSee('Panel de Admin');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('productos.index'))->assertOk()->assertSee('Panel de Admin');
    }
}
