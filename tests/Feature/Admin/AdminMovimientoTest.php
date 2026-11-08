<?php

namespace Tests\Feature\Admin;

use App\Models\Insumo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMovimientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_una_entrada_y_aumenta_el_stock_del_insumo(): void
    {
        $admin = User::factory()->admin()->create();
        $insumo = Insumo::factory()->create(['nombre' => 'Café en grano', 'stock_actual' => 10]);

        $response = $this->actingAs($admin)->post(route('admin.insumos.movimientos.store', $insumo), [
            'tipo' => 'entrada',
            'cantidad' => 5,
            'motivo' => 'Reabastecimiento semanal',
        ]);

        $response->assertRedirect(route('admin.insumos.index'));
        $this->assertDatabaseHas('insumos', [
            'id' => $insumo->id,
            'stock_actual' => 15,
        ]);
        $this->assertDatabaseHas('movimientos_inventario', [
            'insumo_id' => $insumo->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'user_id' => $admin->id,
        ]);
    }

    public function test_registra_una_salida_y_disminuye_el_stock_del_insumo(): void
    {
        $admin = User::factory()->admin()->create();
        $insumo = Insumo::factory()->create(['nombre' => 'Leche', 'stock_actual' => 20]);

        $this->actingAs($admin)->post(route('admin.insumos.movimientos.store', $insumo), [
            'tipo' => 'salida',
            'cantidad' => 7,
            'motivo' => 'Consumo en producción',
        ]);

        $this->assertDatabaseHas('insumos', [
            'id' => $insumo->id,
            'stock_actual' => 13,
        ]);
    }

    public function test_rechaza_una_salida_que_excede_el_stock_disponible_y_no_modifica_nada(): void
    {
        $admin = User::factory()->admin()->create();
        $insumo = Insumo::factory()->create(['nombre' => 'Vasos', 'stock_actual' => 2]);

        $response = $this->actingAs($admin)->post(route('admin.insumos.movimientos.store', $insumo), [
            'tipo' => 'salida',
            'cantidad' => 10,
            'motivo' => 'Salida inválida',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('cantidad');
        $this->assertDatabaseHas('insumos', [
            'id' => $insumo->id,
            'stock_actual' => 2,
        ]);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_registra_un_ajuste_que_establece_el_stock_fisico(): void
    {
        $admin = User::factory()->admin()->create();
        $insumo = Insumo::factory()->create(['nombre' => 'Café en grano', 'stock_actual' => 10]);

        $this->actingAs($admin)->post(route('admin.insumos.movimientos.store', $insumo), [
            'tipo' => 'ajuste',
            'cantidad' => 25,
            'motivo' => 'Conteo físico',
        ]);

        $this->assertDatabaseHas('insumos', [
            'id' => $insumo->id,
            'stock_actual' => 25,
        ]);
    }

    public function test_redirige_al_invitado_al_login_al_registrar_un_movimiento(): void
    {
        $insumo = Insumo::factory()->create();

        $response = $this->post(route('admin.insumos.movimientos.store', $insumo), [
            'tipo' => 'entrada',
            'cantidad' => 5,
        ]);

        $response->assertRedirect(route('login'));
    }
}
