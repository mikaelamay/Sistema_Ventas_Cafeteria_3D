<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->words(2, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 1, 100),
            'stock' => fake()->numberBetween(0, 100),
            'categoria' => fake()->randomElement(['bebidas', 'postres', 'comida']),
            'imagen_path' => null,
            'modelo_3d_path' => null,
            'activo' => true,
        ];
    }
}
