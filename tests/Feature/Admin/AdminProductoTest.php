<?php

namespace Tests\Feature\Admin;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.productos.index'))->assertRedirect(route('login'));
        $this->get(route('admin.productos.create'))->assertRedirect(route('login'));
        $this->post(route('admin.productos.store'))->assertRedirect(route('login'));
    }

    public function test_customer_receives_403(): void
    {
        $cliente = User::factory()->cliente()->create();

        $this->actingAs($cliente)->get(route('admin.productos.index'))->assertForbidden();
        $this->actingAs($cliente)->get(route('admin.productos.create'))->assertForbidden();
        $this->actingAs($cliente)->post(route('admin.productos.store'), $this->payload())->assertForbidden();
    }

    public function test_admin_can_view_the_product_list_and_forms(): void
    {
        $admin = $this->admin();
        $producto = Producto::factory()->create([
            'nombre' => 'Latte de vainilla',
            'categoria' => 'bebidas',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.productos.index'))
            ->assertOk()
            ->assertSee('Catálogo de productos')
            ->assertSee($producto->nombre)
            ->assertSee('Nuevo producto');

        $this->actingAs($admin)
            ->get(route('admin.productos.create'))
            ->assertOk()
            ->assertSee('Nuevo producto');

        $this->actingAs($admin)
            ->get(route('admin.productos.edit', $producto))
            ->assertOk()
            ->assertSee('Editar producto')
            ->assertSee($producto->nombre);
    }

    public function test_index_can_be_searched_and_filtered_by_status(): void
    {
        $admin = $this->admin();

        Producto::factory()->create([
            'nombre' => 'Latte de vainilla',
            'categoria' => 'bebidas',
            'activo' => true,
        ]);

        Producto::factory()->create([
            'nombre' => 'Tarta de limón',
            'categoria' => 'postres',
            'activo' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.productos.index', ['buscar' => 'Latte']))
            ->assertOk()
            ->assertSee('Latte de vainilla')
            ->assertDontSee('Tarta de limón');

        $this->actingAs($admin)
            ->get(route('admin.productos.index', ['buscar' => 'postres']))
            ->assertOk()
            ->assertSee('Tarta de limón')
            ->assertDontSee('Latte de vainilla');

        $this->actingAs($admin)
            ->get(route('admin.productos.index', ['filtro' => 'activos']))
            ->assertOk()
            ->assertSee('Latte de vainilla')
            ->assertDontSee('Tarta de limón');

        $this->actingAs($admin)
            ->get(route('admin.productos.index', ['filtro' => 'inactivos']))
            ->assertOk()
            ->assertSee('Tarta de limón')
            ->assertDontSee('Latte de vainilla');
    }

    public function test_store_creates_the_product_with_image_and_3d_model(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())->post(route('admin.productos.store'), $this->payload([
            'imagen' => UploadedFile::fake()->image('latte.jpg', 800, 600),
            'modelo_3d' => UploadedFile::fake()->create('latte.glb', 16),
        ]));

        $response
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('success');

        $producto = Producto::sole();

        $this->assertSame('Latte de vainilla', $producto->nombre);
        $this->assertSame('12.50', $producto->precio);
        $this->assertSame(8, $producto->stock);
        $this->assertSame('bebidas', $producto->categoria);
        $this->assertTrue($producto->activo);

        $this->assertNotNull($producto->imagen_path);
        $this->assertStringStartsWith('productos/', $producto->imagen_path);
        $this->assertStringEndsWith('.jpg', $producto->imagen_path);
        Storage::disk('public')->assertExists($producto->imagen_path);

        $this->assertNotNull($producto->modelo_3d_path);
        $this->assertStringStartsWith('productos/', $producto->modelo_3d_path);
        $this->assertStringEndsWith('.glb', $producto->modelo_3d_path);
        Storage::disk('public')->assertExists($producto->modelo_3d_path);
    }

    public function test_store_can_create_a_product_without_files(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.productos.store'), $this->payload([
                'activo' => '0',
            ]))
            ->assertRedirect(route('admin.productos.index'));

        $producto = Producto::sole();

        $this->assertNull($producto->imagen_path);
        $this->assertNull($producto->modelo_3d_path);
        $this->assertFalse($producto->activo);
    }

    public function test_store_validates_required_fields_and_numeric_limits(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.productos.store'), [
            'precio' => -1,
            'stock' => -3,
            'activo' => '1',
        ]);

        $response->assertSessionHasErrors(['nombre', 'precio', 'stock']);
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_store_rejects_invalid_image_and_model_extensions(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.productos.store'), $this->payload([
            'imagen' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
            'modelo_3d' => UploadedFile::fake()->create('modelo.obj', 10),
        ]));

        $response->assertSessionHasErrors(['imagen', 'modelo_3d']);
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_store_rejects_oversized_image_and_model(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.productos.store'), $this->payload([
            'imagen' => UploadedFile::fake()->image('grande.jpg')->size(5000),
            'modelo_3d' => UploadedFile::fake()->create('grande.glb', 16)->size(21000),
        ]));

        $response->assertSessionHasErrors(['imagen', 'modelo_3d']);
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_update_replaces_files_and_deletes_the_previous_ones(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/antigua.jpg', 'old image');
        Storage::disk('public')->put('productos/antiguo.glb', 'old model');

        $producto = Producto::factory()->create([
            'imagen_path' => 'productos/antigua.jpg',
            'modelo_3d_path' => 'productos/antiguo.glb',
        ]);

        $response = $this->actingAs($this->admin())->patch(route('admin.productos.update', $producto), $this->payload([
            'nombre' => 'Latte actualizado',
            'activo' => '0',
            'imagen' => UploadedFile::fake()->image('nueva.png'),
            'modelo_3d' => UploadedFile::fake()->create('nuevo.gltf', 16),
        ]));

        $response
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('success');

        $producto->refresh();

        $this->assertSame('Latte actualizado', $producto->nombre);
        $this->assertFalse($producto->activo);
        $this->assertNotSame('productos/antigua.jpg', $producto->imagen_path);
        $this->assertNotSame('productos/antiguo.glb', $producto->modelo_3d_path);
        $this->assertStringEndsWith('.png', $producto->imagen_path);
        $this->assertStringEndsWith('.gltf', $producto->modelo_3d_path);

        Storage::disk('public')->assertMissing('productos/antigua.jpg');
        Storage::disk('public')->assertMissing('productos/antiguo.glb');
        Storage::disk('public')->assertExists($producto->imagen_path);
        Storage::disk('public')->assertExists($producto->modelo_3d_path);
    }

    public function test_update_preserves_files_when_no_new_upload_is_sent(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/actual.jpg', 'image');
        Storage::disk('public')->put('productos/actual.glb', 'model');

        $producto = Producto::factory()->create([
            'imagen_path' => 'productos/actual.jpg',
            'modelo_3d_path' => 'productos/actual.glb',
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.productos.update', $producto), $this->payload(['nombre' => 'Sin cambios de archivo']))
            ->assertRedirect(route('admin.productos.index'));

        $producto->refresh();

        $this->assertSame('productos/actual.jpg', $producto->imagen_path);
        $this->assertSame('productos/actual.glb', $producto->modelo_3d_path);
        Storage::disk('public')->assertExists('productos/actual.jpg');
        Storage::disk('public')->assertExists('productos/actual.glb');
    }

    public function test_update_can_remove_the_current_files_with_checkboxes(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/quitar.jpg', 'image');
        Storage::disk('public')->put('productos/quitar.glb', 'model');

        $producto = Producto::factory()->create([
            'imagen_path' => 'productos/quitar.jpg',
            'modelo_3d_path' => 'productos/quitar.glb',
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.productos.update', $producto), $this->payload([
                'quitar_imagen' => '1',
                'quitar_modelo' => '1',
            ]))
            ->assertRedirect(route('admin.productos.index'));

        $producto->refresh();

        $this->assertNull($producto->imagen_path);
        $this->assertNull($producto->modelo_3d_path);
        Storage::disk('public')->assertMissing('productos/quitar.jpg');
        Storage::disk('public')->assertMissing('productos/quitar.glb');
    }

    public function test_destroy_deletes_the_product_and_its_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/borrar.jpg', 'image');
        Storage::disk('public')->put('productos/borrar.glb', 'model');

        $producto = Producto::factory()->create([
            'imagen_path' => 'productos/borrar.jpg',
            'modelo_3d_path' => 'productos/borrar.glb',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.productos.destroy', $producto))
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
        Storage::disk('public')->assertMissing('productos/borrar.jpg');
        Storage::disk('public')->assertMissing('productos/borrar.glb');
    }

    public function test_destroy_fails_gracefully_when_the_product_has_orders(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/con-pedido.jpg', 'image');

        $producto = Producto::factory()->create([
            'imagen_path' => 'productos/con-pedido.jpg',
        ]);

        $pedido = Pedido::factory()->create();

        $pedido->detallesPedido()->create([
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 10,
            'subtotal' => 10,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.productos.destroy', $producto))
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('error', 'No se puede eliminar: tiene pedidos asociados.');

        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
        Storage::disk('public')->assertExists('productos/con-pedido.jpg');
    }

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    /**
     * Build a valid product payload.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Latte de vainilla',
            'descripcion' => 'Café con leche y un toque de vainilla.',
            'precio' => '12.50',
            'stock' => 8,
            'categoria' => 'bebidas',
            'activo' => '1',
        ], $overrides);
    }
}
