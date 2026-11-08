<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Seed the public catalogue with the house coffee selection.
     */
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Espresso',
                'descripcion' => 'Un disparo concentrado de nuestro blend de la casa: cuerpo intenso, notas de cacao amargo y un final limpio que invita al siguiente sorbo.',
                'precio' => 2.50,
                'stock' => 40,
                'categoria' => 'Café',
            ],
            [
                'nombre' => 'Latte',
                'descripcion' => 'Espresso de especialidad combinado con leche vaporizada sedosa y una fina capa de microespuma. Dulce, equilibrado y reconfortante.',
                'precio' => 3.80,
                'stock' => 35,
                'categoria' => 'Café',
            ],
            [
                'nombre' => 'Capuchino',
                'descripcion' => 'Nuestro espresso con leche cremosa y una corona generosa de espuma. Un clásico italiano servido en su punto exacto de temperatura.',
                'precio' => 3.60,
                'stock' => 30,
                'categoria' => 'Café',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                ['nombre' => $producto['nombre']],
                [
                    ...$producto,
                    'imagen_path' => null,
                    'modelo_3d_path' => null,
                    'activo' => true,
                ],
            );
        }
    }
}
