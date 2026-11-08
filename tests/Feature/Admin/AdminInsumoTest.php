<?php

namespace Tests\Feature\Admin;

use App\Models\Insumo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInsumoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function permite_al_administrador_listar_insumos(): void
    {
        $admin = User::factory()->admin()->create();
        Insumo::factory()->create(['nombre' => 'Café en grano']);

        $response = $this->actingAs($admin)->get(route('admin.insumos.index'));

        $response->assertOk();
        $this->assertDatabaseHas('insumos', ['nombre' => 'Café en grano']);
    }

    /** @test */
    public function redirige_al_invitado_al_login_al_acceder_a_insumos(): void
    {
        $response = $this->get(route('admin.insumos.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function devuelve_403_al_cliente_al_acceder_a_insumos(): void
    {
        $cliente = User::factory()->cliente()->create();

        $response = $this->actingAs($cliente)->get(route('admin.insumos.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function crea_un_insumo_y_lo_muestra_en_el_listado(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.insumos.store'), [
            'nombre' => 'Café en grano',
            'unidad_medida' => 'kg',
            'stock_actual' => 10,
            'stock_minimo' => 2,
            'costo_unitario' => 18.50,
        ]);

        $response->assertRedirect(route('admin.insumos.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('insumos', [
            'nombre' => 'Café en grano',
            'unidad_medida' => 'kg',
            'stock_actual' => 10,
        ]);
    }

    /** @test */
    public function rechaza_un_insumo_con_stock_negativo(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.insumos.store'), [
            'nombre' => 'Leche',
            'unidad_medida' => 'lt',
            'stock_actual' => -5,
            'stock_minimo' => 1,
        ]);

        $response->assertSessionHasErrors('stock_actual');
        $this->assertDatabaseMissing('insumos', ['nombre' => 'Leche']);
    }

    /** @test */
    public function actualiza_un_insumo_existente(): void
    {
        $admin = User::factory()->admin()->create();
        $insumo = Insumo::create(['nombre' => 'Leche', 'unidad_medida' => 'lt', 'stock_actual' => 20, 'stock_minimo' => 5]);

        $this->actingAs($admin)->patch(route('admin.insumos.update', $insumo), [
            'nombre' => 'Leche entera',
            'unidad_medida' => 'lt',
            'stock_actual' => 25,
            'stock_minimo' => 5,
        ]);

        $this->assertDatabaseHas('insumos', [
            'id' => $insumo->id,
            'nombre' => 'Leche entera',
            'stock_actual' => 25,
        ]);
    }

    /** @test */
    public function no_elimina_un_insumo_que_tiene_movimientos_asociados(): void
    {
        $admin = User::factory()->admin()->create();
        $insumo = Insumo::create(['nombre' => 'Vasos', 'unidad_medida' => 'unidad', 'stock_actual' => 10, 'stock_minimo' => 2]);
        $insumo->movimientosInventario()->create([
            'tipo' => 'entrada',
            'cantidad' => 10,
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.insumos.destroy', $insumo));

        $response->assertRedirect(route('admin.insumos.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('insumos', ['id' => $insumo->id]);
    }
}
