<?php

namespace Tests\Feature\Admin;

use App\Enums\EstadoPedido;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_permite_al_administrador_ver_el_reporte_de_ventas_por_periodo(): void
    {
        $admin = User::factory()->admin()->create();

        $producto = Producto::factory()->create(['nombre' => 'Latte', 'precio' => 3.80]);
        $pedido = Pedido::factory()->create([
            'user_id' => $admin->id,
            'estado' => EstadoPedido::Entregado,
            'total' => 3.80,
        ]);
        DetallePedido::factory()->create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 3.80,
            'subtotal' => 3.80,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reportes.ventasPeriodo'));

        $response->assertOk();
        $response->assertSee('Total vendido');
        $response->assertSee('Número de pedidos');
    }

    public function test_filtra_ventas_por_rango_de_fechas(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create();

        Pedido::factory()->create([
            'estado' => EstadoPedido::Entregado,
            'total' => 10,
            'created_at' => now()->subDays(5),
        ]);
        Pedido::factory()->create([
            'estado' => EstadoPedido::Entregado,
            'total' => 20,
            'created_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reportes.ventasPeriodo', [
            'fecha_inicio' => now()->subDays(2)->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertSee('S/ 20.00');
        $response->assertDontSee('S/ 10.00');
    }

    public function test_muestra_el_ranking_de_productos_mas_vendidos(): void
    {
        $admin = User::factory()->admin()->create();
        $cafe = Producto::factory()->create(['nombre' => 'Espresso']);
        $leche = Producto::factory()->create(['nombre' => 'Latte']);

        $pedido = Pedido::factory()->create(['estado' => EstadoPedido::Entregado, 'total' => 0]);
        DetallePedido::factory()->create(['pedido_id' => $pedido->id, 'producto_id' => $cafe->id, 'cantidad' => 5, 'precio_unitario' => 2.50, 'subtotal' => 12.50]);
        DetallePedido::factory()->create(['pedido_id' => $pedido->id, 'producto_id' => $leche->id, 'cantidad' => 2, 'precio_unitario' => 3.80, 'subtotal' => 7.60]);

        $response = $this->actingAs($admin)->get(route('admin.reportes.ventasProducto'));

        $response->assertOk();
        $response->assertSee('Espresso');
        $response->assertSee('Latte');
        $response->assertSeeInOrder(['Espresso', 'Latte']);
    }

    public function test_redirige_al_invitado_al_login_al_acceder_a_reportes(): void
    {
        $this->get(route('admin.reportes.ventasPeriodo'))->assertRedirect(route('login'));
        $this->get(route('admin.reportes.ventasProducto'))->assertRedirect(route('login'));
    }

    public function test_devuelve_403_al_cliente_al_acceder_a_reportes(): void
    {
        $cliente = User::factory()->cliente()->create();

        $this->actingAs($cliente)->get(route('admin.reportes.ventasPeriodo'))->assertStatus(403);
        $this->actingAs($cliente)->get(route('admin.reportes.ventasProducto'))->assertStatus(403);
    }
}
